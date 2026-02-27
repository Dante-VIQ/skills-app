# app/Services/Python/skilldna_recommender.py

#!/usr/bin/env python3

import sys
import json
import pickle
import numpy as np
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
from sklearn.neighbors import NearestNeighbors
from sklearn.decomposition import TruncatedSVD
import warnings
warnings.filterwarnings('ignore')

class SkillDNARecommender:
    """
    Complete recommendation system for SkillDNA
    """

    def __init__(self):
        # Content-based components
        self.tfidf = TfidfVectorizer(
            max_features=5000,
            stop_words='english',
            ngram_range=(1, 2)
        )

        # Collaborative filtering
        self.user_knn = NearestNeighbors(metric='cosine', algorithm='brute', n_neighbors=20)
        self.item_knn = NearestNeighbors(metric='cosine', algorithm='brute', n_neighbors=20)

        # Matrix factorization
        self.svd = TruncatedSVD(n_components=50, random_state=42)

        # Data storage
        self.content_df = None
        self.content_features = None
        self.content_similarity = None
        self.user_item_matrix = None
        self.user_features = None
        self.item_features = None

        # Weights for different user types
        self.weights = {
            'child': {'content': 0.2, 'collaborative': 0.6, 'popular': 0.2},
            'parent': {'content': 0.4, 'collaborative': 0.3, 'popular': 0.3},
            'tutor': {'content': 0.6, 'collaborative': 0.2, 'popular': 0.2}
        }

    def prepare_content(self, content_data):
        """
        Prepare content features from database
        """
        self.content_df = pd.DataFrame(content_data)

        # Combine text features
        self.content_df['text_features'] = (
            self.content_df['title'].fillna('') + ' ' +
            self.content_df['description'].fillna('') + ' ' +
            self.content_df['category_name'].fillna('') + ' ' +
            self.content_df['tags'].apply(lambda x: ' '.join(x) if isinstance(x, list) else '')
        )

        # Create TF-IDF features
        self.content_features = self.tfidf.fit_transform(self.content_df['text_features'])

        # Calculate similarity matrix
        self.content_similarity = cosine_similarity(self.content_features)

        print(f"✅ Content features prepared: {self.content_features.shape}")
        return self.content_features

    def prepare_interactions(self, interaction_data):
        """
        Prepare user-item interaction matrix
        """
        self.interaction_df = pd.DataFrame(interaction_data)

        if len(self.interaction_df) == 0:
            print("⚠️ No interaction data available")
            self.user_item_matrix = None
            return None

        # Create user-item matrix with weighted interactions
        # Weight different interaction types
        weights = {'view': 0.1, 'watch': 0.3, 'complete': 0.5, 'like': 0.4, 'save': 0.6}

        self.interaction_df['weight'] = self.interaction_df['type'].map(weights).fillna(0.1)
        self.interaction_df['value'] = self.interaction_df['weight'] * (self.interaction_df['rating'].fillna(3) / 5)

        # Pivot to create matrix
        self.user_item_matrix = self.interaction_df.pivot_table(
            index='user_id',
            columns='content_id',
            values='value',
            fill_value=0
        )

        # Prepare item features for KNN
        if self.content_features is not None:
            self.item_features = self.content_features
        else:
            self.item_features = self.user_item_matrix.T.values

        # Fit nearest neighbors models
        if len(self.user_item_matrix) > 1:
            self.user_knn.fit(self.user_item_matrix.values)
        if len(self.item_features) > 1:
            self.item_knn.fit(self.item_features)

        print(f"✅ Interaction matrix prepared: {self.user_item_matrix.shape}")
        return self.user_item_matrix

    def get_content_based_recs(self, user_id, interactions, n=10):
        """
        Content-based recommendations using last interacted content
        """
        if len(interactions) == 0 or self.content_df is None:
            return []

        # Get last interacted content
        last_interaction = sorted(interactions, key=lambda x: x['timestamp'], reverse=True)[0]
        last_content_id = last_interaction['content_id']

        # Find index of this content
        content_idx = self.content_df[self.content_df['id'] == last_content_id].index
        if len(content_idx) == 0:
            return []

        idx = content_idx[0]

        # Get similarity scores
        sim_scores = list(enumerate(self.content_similarity[idx]))
        sim_scores = sorted(sim_scores, key=lambda x: x[1], reverse=True)[1:n+1]

        # Get content IDs
        content_indices = [i[0] for i in sim_scores]
        scores = [i[1] for i in sim_scores]

        recommendations = []
        for i, score in zip(content_indices, scores):
            recommendations.append({
                'id': int(self.content_df.iloc[i]['id']),
                'score': float(score),
                'reason': 'content_based'
            })

        return recommendations

    def get_collaborative_recs(self, user_id, interactions, n=10):
        """
        Collaborative filtering recommendations
        """
        if self.user_item_matrix is None or user_id not in self.user_item_matrix.index:
            return []

        # Get user vector
        user_idx = self.user_item_matrix.index.get_loc(user_id)
        user_vector = self.user_item_matrix.iloc[user_idx].values.reshape(1, -1)

        # Find similar users
        if len(self.user_item_matrix) < 2:
            return []

        distances, indices = self.user_knn.kneighbors(
            user_vector,
            n_neighbors=min(11, len(self.user_item_matrix))
        )

        # Get similar users (excluding self)
        similar_users = indices[0][1:]

        if len(similar_users) == 0:
            return []

        # Aggregate their preferences
        similar_users_data = self.user_item_matrix.iloc[similar_users]
        item_scores = similar_users_data.mean(axis=0)

        # Remove items user already interacted with
        user_interactions = self.user_item_matrix.iloc[user_idx]
        already_interacted = user_interactions[user_interactions > 0].index.tolist()

        # Filter and sort
        recommendations = []
        for content_id in item_scores.index:
            if content_id not in already_interacted:
                score = item_scores[content_id]
                if score > 0:
                    recommendations.append({
                        'id': content_id,
                        'score': float(score),
                        'reason': 'user_based'
                    })

        # Sort by score and take top n
        recommendations = sorted(recommendations, key=lambda x: x['score'], reverse=True)[:n]

        return recommendations

    def get_popular_recs(self, interactions, n=10):
        """
        Popularity-based recommendations (fallback)
        """
        if len(interactions) == 0 or self.content_df is None:
            # Return random content
            sample = self.content_df.sample(n=min(n, len(self.content_df)))
            return [
                {'id': row['id'], 'score': 0.5, 'reason': 'random'}
                for _, row in sample.iterrows()
            ]

        # Calculate popularity from interactions
        df = pd.DataFrame(interactions)

        # Count interactions per content
        popularity = df.groupby('content_id').agg({
            'content_id': 'count',
            'duration': 'mean',
            'rating': 'mean'
        }).rename(columns={'content_id': 'count'})

        # Normalize
        if 'count' in popularity.columns and len(popularity) > 0:
            popularity['count_norm'] = popularity['count'] / popularity['count'].max()
            popularity['duration_norm'] = popularity['duration'].fillna(0) / popularity['duration'].max() if popularity['duration'].max() > 0 else 0
            popularity['rating_norm'] = popularity['rating'].fillna(0) / 5

            # Combined score
            popularity['popularity'] = (
                popularity['count_norm'] * 0.5 +
                popularity['duration_norm'] * 0.3 +
                popularity['rating_norm'] * 0.2
            )

            # Sort and get top n
            top_popular = popularity.nlargest(n, 'popularity')

            recommendations = []
            for content_id, row in top_popular.iterrows():
                recommendations.append({
                    'id': content_id,
                    'score': float(row['popularity']),
                    'reason': 'popular'
                })

            return recommendations

        return []

    def get_new_recs(self, n=10):
        """
        New content recommendations
        """
        if self.content_df is None:
            return []

        # Sort by creation date (assuming id is sequential)
        newest = self.content_df.sort_values('id', ascending=False).head(n)

        return [
            {'id': row['id'], 'score': 0.6, 'reason': 'new'}
            for _, row in newest.iterrows()
        ]

    def get_hybrid_recommendations(self, user_id, user_type, interactions, n=10):
        """
        Combine multiple recommendation strategies
        """
        weights = self.weights.get(user_type, self.weights['child'])

        # Get recommendations from each method
        content_recs = self.get_content_based_recs(user_id, interactions, n)
        collab_recs = self.get_collaborative_recs(user_id, interactions, n)
        popular_recs = self.get_popular_recs(interactions, n)
        new_recs = self.get_new_recs(n)

        # Combine with weights
        all_recs = {}

        for rec in content_recs:
            all_recs[rec['id']] = {
                'score': rec['score'] * weights['content'],
                'reason': rec['reason'],
                'count': 1
            }

        for rec in collab_recs:
            if rec['id'] in all_recs:
                all_recs[rec['id']]['score'] += rec['score'] * weights['collaborative']
                all_recs[rec['id']]['count'] += 1
            else:
                all_recs[rec['id']] = {
                    'score': rec['score'] * weights['collaborative'],
                    'reason': rec['reason'],
                    'count': 1
                }

        for rec in popular_recs:
            if rec['id'] in all_recs:
                all_recs[rec['id']]['score'] += rec['score'] * weights['popular']
                all_recs[rec['id']]['count'] += 1
            else:
                all_recs[rec['id']] = {
                    'score': rec['score'] * weights['popular'],
                    'reason': rec['reason'],
                    'count': 1
                }

        for rec in new_recs:
            if rec['id'] in all_recs:
                all_recs[rec['id']]['score'] += rec['score'] * 0.1  # Small weight for new
                all_recs[rec['id']]['count'] += 1
            else:
                all_recs[rec['id']] = {
                    'score': rec['score'] * 0.1,
                    'reason': rec['reason'],
                    'count': 1
                }

        # Normalize scores by count and convert to list
        recommendations = []
        for content_id, data in all_recs.items():
            # Normalize score by number of contributions
            final_score = data['score'] / data['count'] if data['count'] > 0 else data['score']

            # Boost if multiple methods recommend it
            if data['count'] > 1:
                final_score *= (1 + 0.1 * data['count'])

            recommendations.append({
                'id': content_id,
                'score': min(final_score, 1.0),  # Cap at 1.0
                'reason': data['reason'],
                'type': 'hybrid'
            })

        # Sort by score and take top n
        recommendations = sorted(recommendations, key=lambda x: x['score'], reverse=True)[:n]

        return recommendations

    def get_similar_content(self, content_id, n=10):
        """
        Get content similar to a specific resource
        """
        if self.content_df is None or content_id not in self.content_df['id'].values:
            return []

        # Find index
        idx = self.content_df[self.content_df['id'] == content_id].index[0]

        # Get similarity scores
        sim_scores = list(enumerate(self.content_similarity[idx]))
        sim_scores = sorted(sim_scores, key=lambda x: x[1], reverse=True)[1:n+1]

        recommendations = []
        for i, score in sim_scores:
            recommendations.append({
                'id': int(self.content_df.iloc[i]['id']),
                'score': float(score),
                'reason': 'similar_content'
            })

        return recommendations

    def save(self, path):
        """
        Save model to disk
        """
        with open(path, 'wb') as f:
            pickle.dump(self, f)
        print(f"✅ Model saved to {path}")

    @classmethod
    def load(cls, path):
        """
        Load model from disk
        """
        with open(path, 'rb') as f:
            model = pickle.load(f)
        print(f"✅ Model loaded from {path}")
        return model


def main():
    """
    Main entry point for command-line usage
    """
    if len(sys.argv) < 2:
        print(json.dumps({"error": "No input file provided"}))
        sys.exit(1)

    input_file = sys.argv[1]

    try:
        with open(input_file, 'r') as f:
            input_data = json.load(f)

        # Check if we're training or predicting
        if input_data.get('action') == 'train':
            # Training mode
            from database import get_content_data, get_interaction_data

            content_data = get_content_data()
            interaction_data = get_interaction_data()

            model = SkillDNARecommender()
            model.prepare_content(content_data)
            model.prepare_interactions(interaction_data)

            output_path = input_data.get('output_path', 'skilldna_recommender.pkl')
            model.save(output_path)

            print(json.dumps({"success": True, "message": "Model trained successfully"}))

        elif input_data.get('type') == 'similar':
            # Similar content mode
            model_path = input_data.get('model_path', 'skilldna_recommender.pkl')
            model = SkillDNARecommender.load(model_path)

            content_id = input_data['resource_id']
            n = input_data.get('n_recommendations', 10)

            recommendations = model.get_similar_content(content_id, n)

            print(json.dumps({
                "success": True,
                "recommendations": recommendations
            }))

        else:
            # Recommendation mode
            model_path = input_data.get('model_path', 'skilldna_recommender.pkl')

            try:
                model = SkillDNARecommender.load(model_path)
            except FileNotFoundError:
                # If model doesn't exist, create a new one with available data
                model = SkillDNARecommender()

                # We'd need to fetch data here - in practice, this would come from Laravel
                # For now, create a minimal model
                if 'content_data' in input_data:
                    model.prepare_content(input_data['content_data'])
                if 'interaction_data' in input_data:
                    model.prepare_interactions(input_data['interaction_data'])

            user_id = input_data['user_id']
            user_type = input_data['user_type']
            interactions = input_data.get('interactions', [])
            n = input_data.get('n_recommendations', 10)

            recommendations = model.get_hybrid_recommendations(
                user_id=user_id,
                user_type=user_type,
                interactions=interactions,
                n=n
            )

            print(json.dumps({
                "success": True,
                "recommendations": recommendations
            }))

    except Exception as e:
        print(json.dumps({
            "success": False,
            "error": str(e)
        }))
        sys.exit(1)


if __name__ == "__main__":
    main()
