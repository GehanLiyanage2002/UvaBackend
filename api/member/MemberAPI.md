# Member API Documentation
## Overview
This document provides details for the Project Management, Task Management, Notice Management, Supervisor Management, and Event Management API endpoints available to users with the `member` role. The design is minimalist and focuses on clarity. Members can only perform read-only (GET) operations to view projects, tasks, notices, supervisors, and events they are associated with.

## Base URLs
- **Project Management**: `/api/member/projects/project.php`
- **Task Management**: `/api/member/tasks/task.php`
- **Notice Management**: `/api/member/notices/notice.php`
- **Supervisor Management**: `/api/member/supervisors/supervisor.php`
- **Event Management**: `/api/member/events/event.php`

## Project Management Endpoints

### 1. List Projects
- **Method**: GET
- **URL**: `/api/member/projects/project.php?action=list`
- **Description**: Retrieves a list of all projects associated with the authenticated member.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "tasks": [{ "id": 1, "title": "Project Title", "description": "Project Description", ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 2. View Project
- **Method**: GET
- **URL**: `/api/member/projects/project.php?action=view&id={projectId}`
- **Description**: Retrieves details of a specific project by its ID, accessible to the authenticated member.
- **Query Parameters**:
  - `id` (integer, required): The ID of the project.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "task": { "id": 1, "title": "Project Title", "description": "Project Description", ... }
    }
    ```
  - **400 Bad Request**: If `id` is invalid.
  - **404 Not Found**: If the project is not found or the member does not have access.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

## Task Management Endpoints

### 1. List Tasks
- **Method**: GET
- **URL**: `/api/member/tasks/task.php?action=list&id={projectId}`
- **Description**: Retrieves a list of tasks assigned to the authenticated member for a specific project.
- **Query Parameters**:
  - `id` (integer, required): The ID of the project.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "tasks": [{ "id": 1, "title": "Task Title", "status": "todo", ... }]
    }
    ```
  - **400 Bad Request**: If `id` is invalid.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

## Notice Management Endpoints

### 1. List Notices
- **Method**: GET
- **URL**: `/api/member/notices/notice.php?action=list`
- **Description**: Retrieves a list of all notices, with optional filtering, accessible to the authenticated member.
- **Query Parameters**:
  - `status` (string, optional): Filter by status (e.g., `active`, `inactive`).
  - `priority` (string, optional): Filter by priority (e.g., `high`, `normal`, `low`).
  - `is_public` (boolean, optional): Filter by public status (`1` for true, `0` for false).
  - `page` (integer, optional): Page number for pagination.
  - `limit` (integer, optional): Number of notices per page.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "data": [{ "id": 1, "title": "Notice Title", "content": "Notice content", "priority": "high", "status": "active", ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 2. View Notice
- **Method**: GET
- **URL**: `/api/member/notices/notice.php?action=view&id={noticeId}`
- **Description**: Retrieves details of a specific notice by its ID, accessible to the authenticated member.
- **Query Parameters**:
  - `id` (integer, required): The ID of the notice.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "notice": { "id": 1, "title": "Notice Title", "content": "Notice content", "priority": "high", "status": "active", ... }
    }
    ```
  - **400 Bad Request**: If `id` is invalid.
  - **404 Not Found**: If the notice is not found.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

## Supervisor Management Endpoints

### 1. List Supervisors
- **Method**: GET
- **URL**: `/api/member/supervisors/supervisor.php?action=list`
- **Description**: Retrieves a list of all supervisors, with optional filtering, accessible to the authenticated member.
- **Query Parameters**:
  - `type` (string, optional): Filter by supervisor type.
  - `faculty_name` (string, optional): Filter by faculty name.
  - `department_name` (string, optional): Filter by department name.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "supervisors": [{ "id": 1, "full_name": "John Doe", "email": "john@example.com", "type": "supervisor", ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 2. View Supervisor
- **Method**: GET
- **URL**: `/api/member/supervisors/supervisor.php?action=view&id={supervisorId}`
- **Description**: Retrieves details of a specific supervisor by their ID, accessible to the authenticated member.
- **Query Parameters**:
  - `id` (integer, required): The ID of the supervisor.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "supervisor": { "id": 1, "full_name": "John Doe", "email": "john@example.com", "type": "supervisor", ... }
    }
    ```
  - **400 Bad Request**: If `id` is invalid.
  - **404 Not Found**: If the supervisor is not found.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

## Event Management Endpoints

### 1. List All Events
- **Method**: GET
- **URL**: `/api/member/events/event.php?action=list`
- **Description**: Retrieves a list of all events associated with the authenticated member.
- **Query Parameters**:
  - `order_by` (string, optional): Field to sort by (default: `date`).
  - `order_direction` (string, optional): Sort direction (`ASC` or `DESC`, default: `ASC`).
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "events": [{ "id": 1, "title": "Event Title", "date": "2024-12-25", ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 2. Get Event by ID
- **Method**: GET
- **URL**: `/api/member/events/event.php?action=get&id={eventId}`
- **Description**: Retrieves details of a specific event by its ID, accessible to the authenticated member.
- **Query Parameters**:
  - `id` (integer, required): The ID of the event.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "event": { "id": 1, "title": "Event Title", "date": "2024-12-25", ... }
    }
    ```
  - **400 Bad Request**: If `id` is invalid.
  - **404 Not Found**: If the event is not found or inaccessible to the member.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 3. Get Upcoming Events
- **Method**: GET
- **URL**: `/api/member/events/event.php?action=upcoming`
- **Description**: Retrieves upcoming events for the authenticated member.
- **Query Parameters**:
  - `limit` (integer, optional): Maximum number of events to return (default: 10).
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "events": [{ "id": 1, "title": "Event Title", "date": "2024-12-25", ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 4. Get Today's Events
- **Method**: GET
- **URL**: `/api/member/events/event.php?action=today`
- **Description**: Retrieves events scheduled for today, accessible to the authenticated member.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "events": [{ "id": 1, "title": "Event Title", "date": "2024-12-25", ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 5. Get This Week's Events
- **Method**: GET
- **URL**: `/api/member/events/event.php?action=this_week`
- **Description**: Retrieves events scheduled for the current week, accessible to the authenticated member.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "events": [{ "id": 1, "title": "Event Title", "date": "2024-12-25", ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 6. Get This Month's Events
- **Method**: GET
- **URL**: `/api/member/events/event.php?action=this_month`
- **Description**: Retrieves events scheduled for the current month, accessible to the authenticated member.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "events": [{ "id": 1, "title": "Event Title", "date": "2024-12-25", ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 7. Get Events by Month
- **Method**: GET
- **URL**: `/api/member/events/event.php?action=by_month`
- **Description**: Retrieves events for a specific month and year, accessible to the authenticated member.
- **Query Parameters**:
  - `year` (integer, optional): Year (default: current year).
  - `month` (integer, optional): Month (1-12, default: current month).
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "events": [{ "id": 1, "title": "Event Title", "date": "2024-12-25", ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 8. Get Events by Date Range
- **Method**: GET
- **URL**: `/api/member/events/event.php?action=date_range`
- **Description**: Retrieves events within a specified date range, accessible to the authenticated member.
- **Query Parameters**:
  - `start_date` (string, required): Start date (YYYY-MM-DD).
  - `end_date` (string, required): End date (YYYY-MM-DD).
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "events": [{ "id": 1, "title": "Event Title", "date": "2024-12-25", ... }]
    }
    ```
  - **400 Bad Request**: If `start_date` or `end_date` is missing.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 9. Get Events Overview
- **Method**: GET
- **URL**: `/api/member/events/event.php?action=overview`
- **Description**: Retrieves a summary of events for the authenticated member.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "overview": { ... }
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 10. Search Events
- **Method**: GET
- **URL**: `/api/member/events/event.php?action=search`
- **Description**: Searches events based on a query string and optional date range, accessible to the authenticated member.
- **Query Parameters**:
  - `query` (string, required): Search term.
  - `start_date` (string, optional): Start date (YYYY-MM-DD).
  - `end_date` (string, optional): End date (YYYY-MM-DD).
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "events": [{ "id": 1, "title": "Event Title", "date": "2024-12-25", ... }]
    }
    ```
  - **400 Bad Request**: If `query` is missing.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 11. Get Events with Meet Links
- **Method**: GET
- **URL**: `/api/member/events/event.php?action=with_meet_links`
- **Description**: Retrieves events with associated meeting links, accessible to the authenticated member.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "events": [{ "id": 1, "title": "Event Title", "meet_link": "https://...", ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

## Authentication
All endpoints require a valid authentication token with the `member` role. Members can only view projects, tasks, notices, supervisors, and events they are associated with. Include the token in the `Authorization` header.

## Error Responses
- **400 Bad Request**: Invalid input parameters.
- **401 Unauthorized**: Authentication failed or insufficient permissions.
- **404 Not Found**: Resource not found or inaccessible to the member.
- **405 Method Not Allowed**: Unsupported HTTP method (only GET is allowed).
- **500 Server Error**: Unexpected server error.