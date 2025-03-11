# Electric Billing Management System

## Overview
A virtual Electric Billing System designed for managing electricity bills and simulating payments. It features user authentication and real-time tracking, with all transactions conducted virtually for testing. The application is hosted locally and is not available online.

## Features
- User authentication
- Real-time tracking of electricity consumption
- Bill management and payment simulation
- Feedback system for users
- Admin dashboard for managing users and plans

## Requirements
- PHP
- A web server (e.g., Apache)
- Composer for dependency management
- MySQL or any compatible database

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/Issam-Almuallem/Senior-Project-Electric-Billing-System-.git

## Database Schema

The Electric Billing Management System (EBMS) uses a MySQL database with the following tables:

### 1. `admin`
- **Purpose**: Stores admin login details.
- **Columns**:
  - `Admin_ID`: Unique identifier for the admin (int).
  - `Email`: Admin's email address (varchar).
  - `Pass`: Admin's hashed password (varchar).

### 2. `comments`
- **Purpose**: Stores user comments and feedback.
- **Columns**:
  - `Com_ID`: Unique identifier for the comment (int).
  - `Comment_Text`: The text of the comment (text).
  - `Time`: Timestamp of when the comment was made (timestamp).
  - `User_ID`: Foreign key referencing the user who made the comment (int).
  - `Reply_Status`: Indicates if the comment has been replied to (tinyint).
  - `Reply_Text`: Text of the reply (text).

### 3. `consumptionrecords`
- **Purpose**: Records electricity consumption for users.
- **Columns**:
  - `Record_ID`: Unique identifier for the consumption record (int).
  - `Consumption`: Amount of electricity consumed (decimal).
  - `User_ID`: Foreign key referencing the user (int).

### 4. `creditcard`
- **Purpose**: Stores credit card information.
- **Columns**:
  - `Credit_num`: Credit card number (varchar).
  - `Balance`: Available balance on the card (int).
  - `CreditCardType`: Type of credit card (varchar).

### 5. `payments`
- **Purpose**: Records payment transactions.
- **Columns**:
  - `PID`: Unique identifier for the payment (int).
  - `Amount`: Amount paid (int).
  - `Date`: Date of the payment (date).
  - `User_ID`: Foreign key referencing the user who made the payment (int).
  - `payment_type`: Type of payment (varchar).

### 6. `plan`
- **Purpose**: Stores different electricity plans.
- **Columns**:
  - `Plan_ID`: Unique identifier for the plan (int).
  - `Name`: Name of the plan (varchar).
  - `Price`: Cost of the plan (decimal).
  - `Amperage`: Amperage provided by the plan (int).
  - `Admin_ID`: Foreign key referencing the admin (int).

### 7. `ratings`
- **Purpose**: Stores user ratings for services.
- **Columns**:
  - `Rating_ID`: Unique identifier for the rating (int).
  - `Rating_Value`: Value of the rating (tinyint).
  - `User_ID`: Foreign key referencing the user (int).

### 8. `subscriptions`
- **Purpose**: Tracks user subscriptions to plans.
- **Columns**:
  - `subsc_ID`: Unique identifier for the subscription (int).
  - `Start_D`: Start date of the subscription (date).
  - `End_D`: End date of the subscription (date).
  - `User_ID`: Foreign key referencing the user (int).
  - `Plan_ID`: Foreign key referencing the plan (int).

### 9. `users`
- **Purpose**: Stores user information.
- **Columns**:
  - `ID`: Unique identifier for the user (int).
  - `Fname`: User's first name (varchar).
  - `Lname`: User's last name (varchar).
  - `Email`: User's email address (varchar).
  - `Pass`: User's hashed password (varchar).
  - `Phone`: User's phone number (varchar).
  - `Address`: User's address (text).
  - `PID`: Foreign key referencing the payment method (int).
  - `Credit_num`: Foreign key referencing the credit card (varchar).

### Relationships
- The database utilizes foreign keys to establish relationships between users, their comments, payments, ratings, and subscriptions.
