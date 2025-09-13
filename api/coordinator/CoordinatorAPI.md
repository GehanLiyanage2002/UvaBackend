# Coordinator API Documentation
## Overview
This document provides details for the Notice Management, Supervisor Management, Event Management, Project Management, and Task Management API endpoints available to coordinators. The design is minimalist and focuses on clarity. All endpoints require authentication with a `coordinator` or `manager` role. Managers and members are restricted to read-only (GET) operations for Notice Management, but both roles can perform all operations for Supervisor Management. For Event, Project, and Task Management, only coordinators can perform operations, which are limited to GET requests.

## Base URLs
- **Notice Management**: `/api/coordinator/notices/notice.php`
- **Supervisor Management**: `/api/coordinator/supervisors/supervisor.php`
- **Event Management**: `/api/coordinator/events/event.php`
- **Project Management**: `/api/coordinator/projects/project.php`
- **Task Management**: `/api/coordinator/tasks/task.php`

## Notice Management Endpoints

### 1. List All Notices
- **Method**: GET
- **URL**: `/api/coordinator/notices/notice.php?action=list`
- **Description**: Retrieves a list of all notices, with optional filtering.
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
- **URL**: `/api/coordinator/notices/notice.php?action=view&id={noticeId}`
- **Description**: Retrieves details of a specific notice by its ID.
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

### 3. Create Notice
- **Method**: POST
- **URL**: `/api/coordinator/notices/notice.php?action=create`
- **Description**: Creates a new notice (coordinator role only).
- **Body** (application/json):
  ```json
  {
    "title": "Important Notice",
    "content": "This is notice content",
    "priority": "high",
    "status": "active",
    "expires_at": "2024-12-31 23:59:59",
    "is_public": true
  }
  ```
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "notice": { "id": 1, "title": "Important Notice", "content": "This is notice content", ... }
    }
    ```
  - **400 Bad Request**: If `title` or `content` is missing or invalid.
  - **401 Unauthorized**: If authentication fails.
  - **403 Forbidden**: If the user is a manager or member.
  - **500 Server Error**: If a server error occurs.

### 4. Update Notice
- **Method**: POST or PUT
- **URL**: `/api/coordinator/notices/notice.php?action=update` (POST) or `/api/coordinator/notices/notice.php?action=update&id={noticeId}` (PUT)
- **Description**: Updates an existing notice (coordinator role only).
- **Query Parameters** (for PUT):
  - `id` (integer, required): The ID of the notice.
- **Body** (application/json):
  ```json
  {
    "id": 1,
    "title": "Updated Notice",
    "content": "Updated content",
    "priority": "normal",
    "status": "active",
    "expires_at": "2024-12-31 23:59:59",
    "is_public": true
  }
  ```
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "notice": { "id": 1, "title": "Updated Notice", "content": "Updated content", ... }
    }
    ```
  - **400 Bad Request**: If `id` is invalid or update fails.
  - **401 Unauthorized**: If authentication fails.
  - **403 Forbidden**: If the user is a manager or member.
  - **500 Server Error**: If a server error occurs.

### 5. Delete Notice
- **Method**: DELETE
- **URL**: `/api/coordinator/notices/notice.php?action=delete&id={noticeId}`
- **Description**: Deletes a notice by its ID (coordinator role only).
- **Query Parameters**:
  - `id` (integer, required): The ID of the notice.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "message": "Notice deleted successfully"
    }
    ```
  - **400 Bad Request**: If `id` is invalid or deletion fails.
  - **401 Unauthorized**: If authentication fails.
  - **403 Forbidden**: If the user is a manager or member.
  - **500 Server Error**: If a server error occurs.

## Supervisor Management Endpoints

### 1. List All Supervisors
- **Method**: GET
- **URL**: `/api/coordinator/supervisors/supervisor.php?action=list`
- **Description**: Retrieves a list of all supervisors, with optional filtering.
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
- **URL**: `/api/coordinator/supervisors/supervisor.php?action=view&id={supervisorId}`
- **Description**: Retrieves details of a specific supervisor by their ID.
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

### 3. Create Supervisor
- **Method**: POST
- **URL**: `/api/coordinator/supervisors/supervisor.php?action=create`
- **Description**: Creates a new supervisor.
- **Body** (application/json):
  ```json
  {
    "full_name": "John Doe",
    "email": "john@example.com",
    "contact": "123456789",
    "type": "supervisor",
    "faculty_name": "Engineering",
    "department_name": "IT",
    "about": "Description"
  }
  ```
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "supervisor": { "id": 1, "full_name": "John Doe", "email": "john@example.com", ... }
    }
    ```
  - **400 Bad Request**: If `full_name`, `email`, `type`, `faculty_name`, or `department_name` is missing or invalid.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 4. Update Supervisor
- **Method**: POST or PUT
- **URL**: `/api/coordinator/supervisors/supervisor.php?action=update` (POST) or `/api/coordinator/supervisors/supervisor.php?action=update&id={supervisorId}` (PUT)
- **Description**: Updates an existing supervisor's details.
- **Query Parameters** (for PUT):
  - `id` (integer, required): The ID of the supervisor.
- **Body** (application/json):
  ```json
  {
    "id": 1,
    "full_name": "Updated Name",
    "email": "updated@example.com",
    "contact": "987654321",
    "type": "supervisor",
    "faculty_name": "Engineering",
    "department_name": "IT",
    "about": "Updated description"
  }
  ```
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "supervisor": { "id": 1, "full_name": "Updated Name", "email": "updated@example.com", ... }
    }
    ```
  - **400 Bad Request**: If `id` is invalid or update fails.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 5. Delete Supervisor
- **Method**: DELETE
- **URL**: `/api/coordinator/supervisors/supervisor.php?action=delete&id={supervisorId}`
- **Description**: Deletes a supervisor by their ID.
- **Query Parameters**:
  - `id` (integer, required): The ID of the supervisor.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "message": "Supervisor deleted successfully"
    }
    ```
  - **400 Bad Request**: If `id` is invalid or deletion fails.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 6. Assign Supervisor to Project
- **Method**: POST
- **URL**: `/api/coordinator/supervisors/supervisor.php?action=assign-to-project`
- **Description**: Assigns a supervisor to a project.
- **Body** (application/json):
  ```json
  {
    "project_id": 1,
    "supervisor_id": 2
  }
  ```
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "message": "Supervisor assigned successfully",
      "supervisors": [{ "id": 2, "full_name": "John Doe", ... }]
    }
    ```
  - **400 Bad Request**: If `project_id` or `supervisor_id` is invalid.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 7. Unassign Supervisor from Project
- **Method**: POST
- **URL**: `/api/coordinator/supervisors/supervisor.php?action=unassign-from-project`
- **Description**: Removes a supervisor from a project.
- **Body** (application/json):
  ```json
  {
    "project_id": 1,
    "supervisor_id": 2
  }
  ```
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "message": "Supervisor unassigned successfully",
      "supervisors": [{ ... }]
    }
    ```
  - **400 Bad Request**: If `project_id` or `supervisor_id` is invalid.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

## Event Management Endpoints

### 1. List All Events
- **Method**: GET
- **URL**: `/api/coordinator/events/event.php?action=list`
- **Description**: Retrieves a list of all events for the coordinator, with optional sorting.
- **Query Parameters**:
  - `order_by` (string, optional): Field to sort by (e.g., `date`, default: `date`).
  - `order_direction` (string, optional): Sort direction (e.g., `ASC`, `DESC`, default: `ASC`).
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "events": [{ "id": 1, "title": "Event Title", "date": "2024-12-01", ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 2. View Event
- **Method**: GET
- **URL**: `/api/coordinator/events/event.php?action=get&id={eventId}`
- **Description**: Retrieves details of a specific event by its ID.
- **Query Parameters**:
  - `id` (integer, required): The ID of the event.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "event": { "id": 1, "title": "Event Title", "date": "2024-12-01", ... }
    }
    ```
  - **400 Bad Request**: If `id` is invalid.
  - **404 Not Found**: If the event is not found.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 3. List Upcoming Events
- **Method**: GET
- **URL**: `/api/coordinator/events/event.php?action=upcoming`
- **Description**: Retrieves a list of upcoming events for the coordinator.
- **Query Parameters**:
  - `limit` (integer, optional): Maximum number of events to return (default: 10).
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "events": [{ "id": 1, "title": "Upcoming Event", "date": "2024-12-01", ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 4. List Today's Events
- **Method**: GET
- **URL**: `/api/coordinator/events/event.php?action=today`
- **Description**: Retrieves a list of events scheduled for today.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "events": [{ "id": 1, "title": "Today's Event", "date": "2024-08-16", ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 5. List This Week's Events
- **Method**: GET
- **URL**: `/api/coordinator/events/event.php?action=this_week`
- **Description**: Retrieves a list of events scheduled for the current week.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "events": [{ "id": 1, "title": "Weekly Event", "date": "2024-08-18", ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 6. List This Month's Events
- **Method**: GET
- **URL**: `/api/coordinator/events/event.php?action=this_month`
- **Description**: Retrieves a list of events scheduled for the current month.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "events": [{ "id": 1, "title": "Monthly Event", "date": "2024-08-20", ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 7. List Events by Month
- **Method**: GET
- **URL**: `/api/coordinator/events/event.php?action=by_month`
- **Description**: Retrieves a list of events for a specific month and year.
- **Query Parameters**:
  - `year` (integer, optional): Year of the events (default: current year).
  - `month` (integer, optional): Month of the events (default: current month).
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "events": [{ "id": 1, "title": "Event Title", "date": "2024-12-01", ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 8. List Events by Date Range
- **Method**: GET
- **URL**: `/api/coordinator/events/event.php?action=date_range`
- **Description**: Retrieves a list of events within a specified date range.
- **Query Parameters**:
  - `start_date` (string, required): Start date in `YYYY-MM-DD` format.
  - `end_date` (string, required): End date in `YYYY-MM-DD` format.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "events": [{ "id": 1, "title": "Event Title", "date": "2024-12-15", ... }]
    }
    ```
  - **400 Bad Request**: If `start_date` or `end_date` is missing or invalid.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 9. Get Events Overview
- **Method**: GET
- **URL**: `/api/coordinator/events/event.php?action=overview`
- **Description**: Retrieves an overview of events for the coordinator.
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
- **URL**: `/api/coordinator/events/event.php?action=search`
- **Description**: Searches events based on a query string, with optional date range filtering.
- **Query Parameters**:
  - `query` (string, required): Search term (e.g., `meeting`).
  - `start_date` (string, optional): Start date in `YYYY-MM-DD` format.
  - `end_date` (string, optional): End date in `YYYY-MM-DD` format.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "events": [{ "id": 1, "title": "Meeting Event", "date": "2024-12-01", ... }]
    }
    ```
  - **400 Bad Request**: If `query` is missing.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 11. List Events with Meet Links
- **Method**: GET
- **URL**: `/api/coordinator/events/event.php?action=with_meet_links`
- **Description**: Retrieves a list of events that have associated meeting links.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "events": [{ "id": 1, "title": "Event with Meet Link", "meet_link": "https://meet.example.com", ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

## Project Management Endpoints

### 1. List All Projects
- **Method**: GET
- **URL**: `/api/coordinator/projects/project.php?action=list`
- **Description**: Retrieves a list of all projects for the coordinator.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "tasks": [{ "id": 1, "title": "Project Title", ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 2. View Project
- **Method**: GET
- **URL**: `/api/coordinator/projects/project.php?action=view&id={projectId}`
- **Description**: Retrieves details of a specific project by its ID.
- **Query Parameters**:
  - `id` (integer, required): The ID of the project.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "task": { "id": 1, "title": "Project Title", ... }
    }
    ```
  - **400 Bad Request**: If `id` is invalid.
  - **404 Not Found**: If the project is not found.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

## Task Management Endpoints

### 1. List Tasks by Project
- **Method**: GET
- **URL**: `/api/coordinator/tasks/task.php?action=list&id={projectId}`
- **Description**: Retrieves a list of tasks for a specific project assigned to the coordinator.
- **Query Parameters**:
  - `id` (integer, required): The ID of the project.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "tasks": [{ "id": 1, "title": "Task Title", "project_id": 1, ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

## Authentication
All endpoints require a valid authentication token with the `coordinator` or `manager` role. For Notice Management, managers and members are restricted to GET requests. For Supervisor Management, both coordinators and managers can perform all operations. For Event, Project, and Task Management, only coordinators can perform operations, which are limited to GET requests. Include the token in the `Authorization` header.

## Error Responses
- **400 Bad Request**: Invalid input parameters.
- **401 Unauthorized**: Authentication failed or insufficient permissions.
- **403 Forbidden**: If a manager or member attempts non-GET requests for Notice Management.
- **404 Not Found**: Resource not found.
- **405 Method Not Allowed**: Unsupported HTTP method.
- **500 Server Error**: Unexpected server error.