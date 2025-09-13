# Profile API Documentation
## Overview
This document provides details for the Profile Management API endpoints, available to users with `admin`, `coordinator`, `manager`, or `user` roles. The design is minimalist and focuses on clarity. Users can only view and update their own profiles.

## Base URL
```
/api/profile.php
```

## Profile Management Endpoints

### 1. View Profile
- **Method**: GET
- **URL**: `/api/profile.php?action=view`
- **Description**: Retrieves the authenticated user's profile details.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "profile": { "id": 1, "name": "John Doe", "email": "john@example.com", "academic_year": "2024", "bio": "User bio", ... }
    }
    ```
  - **404 Not Found**: If the profile is not found.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 2. Update Profile
- **Method**: POST or PUT
- **URL**: `/api/profile.php?action=update`
- **Description**: Updates the authenticated user's profile details.
- **Body** (application/json):
  ```json
  {
    "name": "Updated Name",
    "email": "updated@example.com",
    "password": "newpassword123",
    "academic_year": "2025",
    "bio": "Updated bio"
  }
  ```
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "message": "Profile updated successfully",
      "profile": { "id": 1, "name": "Updated Name", "email": "updated@example.com", "academic_year": "2025", "bio": "Updated bio", ... }
    }
    ```
  - **400 Bad Request**: If the update fails (e.g., invalid input).
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

## Authentication
All endpoints require a valid authentication token with one of the following roles: `admin`, `coordinator`, `manager`, or `user`. Users can only view or update their own profile. Include the token in the `Authorization` header.

## Error Responses
- **400 Bad Request**: Invalid input parameters or update failure.
- **401 Unauthorized**: Authentication failed or insufficient permissions.
- **404 Not Found**: Profile not found.
- **405 Method Not Allowed**: Unsupported HTTP method.
- **500 Server Error**: Unexpected server error.