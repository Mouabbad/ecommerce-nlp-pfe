# 🛒 Intelligent E-commerce Platform with NLP Sentiment Analysis

## 📌 Project Overview

This project is an intelligent e-commerce platform developed using **Symfony**, **FastAPI**, and **Natural Language Processing (NLP)** technologies.

The platform allows:

- Clients to browse and purchase products
- Vendors to manage their products
- Customers to leave comments and reviews
- Automatic sentiment analysis of customer comments using AI
- Vendors and administrators to visualize product statistics and customer satisfaction

The project combines:

- Web Development
- Artificial Intelligence

---

# 🎯 Main Objective

The main objective of this project is to integrate **Artificial Intelligence** into an e-commerce platform in order to automatically analyze customer opinions and transform them into useful business insights.

Instead of manually reading all comments, the system can automatically determine whether customer feedback is:

- Positive 😊
- Negative 😡
- Neutral 😐

This helps vendors:

- Improve product quality
- Understand customer satisfaction
- Detect negative reviews quickly
- Make better business decisions

---

# ✨ Custom Development

This project was fully developed **from scratch** without using prebuilt e-commerce templates.

The entire architecture, business logic, dashboards, and NLP integration were manually designed and implemented.

The project includes:

- Custom Symfony controllers
- Custom Doctrine entities
- FastAPI microservice integration
- NLP sentiment analysis workflow
- Dynamic vendor statistics dashboards
- Custom Twig interfaces
- Role-based authentication system
- Comment moderation system
- Custom CSS styling and responsive UI

The communication between Symfony and FastAPI was entirely implemented manually.

---

# 🧠 NLP Integration

The NLP service is developed using:

- FastAPI
- HuggingFace Transformers
- DistilBERT model

Model used:

```text
distilbert-base-uncased-finetuned-sst-2-english
