# Payment Tracker

## Overview

The **Payment Tracker** is a web application designed to help users manage and track their monthly payments easily. It provides a user-friendly interface to monitor payment schedules, amounts, and statuses, ensuring that users never miss a payment.

## Features

- **User-Friendly Interface**: Built with React and styled using Tailwind CSS for a modern look and feel.
- **Payment Management**: Users can add, view, and manage their payment schedules.
- **Real-Time Updates**: The application updates payment statuses dynamically as payments are marked as completed or undone.
- **Service Worker**: Utilizes a service worker for offline capabilities and caching of essential resources.

## Technologies Used

- **React**: A JavaScript library for building user interfaces.
- **Tailwind CSS**: A utility-first CSS framework for styling.
- **Service Workers**: For caching and offline functionality.
- **Babel**: For transpiling modern JavaScript.

## File Structure

```
payment-tracker/
├── icon-192x192.png
├── icon-512x512.png
├── index.html
├── manifest.json
└── sw.js
```

### File Descriptions

- **index.html**: The main HTML file that serves as the entry point for the application. It includes links to the manifest and icons, as well as the necessary scripts for React and Babel.
  
- **manifest.json**: This file provides metadata about the application, including its name, description, icons, and display settings. It allows the app to be installed on devices and provides a native app-like experience.

- **sw.js**: The service worker script that handles caching of resources and enables offline functionality. It listens for install and fetch events to manage cached assets.

## Installation

To run the Payment Tracker locally, follow these steps:

1. **Clone the repository**:
   ```bash
   git clone https://github.com/yourusername/payment-tracker.git
   cd payment-tracker
   ```

2. **Open `index.html` in your browser**:
   Simply open the `index.html` file in your preferred web browser to start using the application.

## Usage

- Upon opening the application, users will see a dashboard displaying their payment schedules.
- Users can mark payments as completed, which updates the payment status and remaining balance.
- The application provides visual indicators for overdue, due today, and upcoming payments.
