from fastapi import FastAPI
from pydantic import BaseModel
from transformers import pipeline

app = FastAPI()

sentiment_pipeline = pipeline("sentiment-analysis", model="distilbert-base-uncased-finetuned-sst-2-english")

class Comment(BaseModel):
    text: str

@app.post("/analyze")
def analyze_sentiment(comment: Comment):
    result = sentiment_pipeline(comment.text)[0]
    label = result['label']
    score = result['score']
    if label == 'POSITIVE' and score < 0.6:
        label = 'NEUTRAL'
    return {"sentiment": label, "score": score}
