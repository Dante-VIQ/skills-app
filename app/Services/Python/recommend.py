# app/Services/Python/recommend.py

#!/usr/bin/env python3

"""
Thin wrapper that loads the model and makes predictions
"""

import sys
import json
import pickle
import os

def main():
    if len(sys.argv) < 2:
        print(json.dumps({"error": "No input file provided"}))
        sys.exit(1)

    input_file = sys.argv[1]

    try:
        with open(input_file, 'r') as f:
            input_data = json.load(f)

        # Load the model
        model_path = input_data.get('model_path', 'skilldna_recommender.pkl')

        if not os.path.exists(model_path):
            # Return fallback recommendations
            print(json.dumps({
                "success": True,
                "recommendations": [],
                "fallback": True
            }))
            return

        with open(model_path, 'rb') as f:
            model = pickle.load(f)

        # Get recommendations
        if input_data.get('type') == 'similar':
            recommendations = model.get_similar_content(
                input_data['resource_id'],
                input_data.get('n_recommendations', 10)
            )
        else:
            recommendations = model.get_hybrid_recommendations(
                user_id=input_data['user_id'],
                user_type=input_data.get('user_type', 'child'),
                interactions=input_data.get('interactions', []),
                n=input_data.get('n_recommendations', 10)
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
