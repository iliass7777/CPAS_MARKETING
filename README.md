# Platform Documentation

## Overview
This is a PHP platform for managing categories of websites (leaders) with ratings and reviews.

## Features
- **Categories**: Organize websites into categories (e.g., "Coding", "General Culture")
- **Websites**: Each category contains a list of websites with ratings
- **Reviews**: Users can submit reviews for websites (pending approval)
- **Back Office**: Full CRUD management for categories, websites, and reviews

## Database Setup

1. Update database credentials in `config/database.php`:
   - `$host`: Database host (default: localhost)
   - `$username`: Database username (default: root)
   - `$password`: Database password (default: empty)
   - `$database`: Database name (default: db_name)

2. Import the database schema:
   ```bash
   mysql -u root -p < config/schema.sql
   ```
   Or manually execute the SQL file in your database management tool.

## File Structure

```
/
├── config/
│   ├── database.php      # Database connection class
│   ├── constants.php     # Constants file
│   └── schema.sql        # Database schema
├── models/
│   ├── Category.php      # Category model
│   ├── Website.php       # Website model
│   └── Review.php        # Review model
├── back-office/
│   ├── index.php         # Dashboard
│   ├── categories.php    # Categories management
│   ├── websites.php      # Websites management
│   └── reviews.php       # Reviews management
├── index.php            # Front-end: Categories list
├── category.php          # Front-end: Websites in category
└── website.php           # Front-end: Website details and reviews
```

## Front-End Pages

### index.php
- Lists all categories
- Link to back office

### category.php?slug={category-slug}
- Shows all websites in a specific category
- Displays ratings and links to website details

### website.php?id={website-id}
- Shows website details
- Displays approved reviews
- Form to submit new reviews (pending approval)

## Back-Office Pages

### Dashboard (back-office/index.php)
- Statistics overview
- Quick links to management pages

### Categories (back-office/categories.php)
- List all categories
- Create, edit, delete categories

### Websites (back-office/websites.php)
- List all websites with ratings
- Create, edit, delete websites
- View website on front-end

### Reviews (back-office/reviews.php)
- List all reviews with status filter
- Approve/reject/pending status management
- Edit or delete reviews
- Status changes automatically update website ratings

## How It Works

1. **Ratings**: Website ratings are automatically calculated from approved reviews
2. **Review Status**: Reviews start as "pending" and must be approved in back-office
3. **Rating Updates**: When a review status changes to/from "approved", the website rating is recalculated

## Usage

1. Set up the database and import schema
2. Configure database credentials
3. Access front-end at: `http://your-domain/index.php`
4. Access back-office at: `http://your-domain/back-office/index.php`
5. Start managing categories, websites, and reviews!

