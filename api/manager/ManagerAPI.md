# API Documentation
## Overview
This document provides details for the Event Management, Task Management, and Project Management API endpoints, including methods, parameters, and responses. The design is minimalist and focuses on clarity. All endpoints require authentication with a `manager` role.

## Base URLs
- **Event Management**: `/api/manager/events/event.php`
- **Task Management**: `/api/manager/tasks/task.php`
- **Project Management**: `/api/manager/projects/`

## Event Management Endpoints

### 1. List All Events
- **Method**: GET
- **URL**: `/api/manager/events/event.php?action=list`
- **Description**: Retrieves a list of all events for the authenticated manager.
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
- **URL**: `/api/manager/events/event.php?action=get&id={eventId}`
- **Description**: Retrieves details of a specific event by its ID.
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
  - **404 Not Found**: If the event is not found.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 3. Get Upcoming Events
- **Method**: GET
- **URL**: `/api/manager/events/event.php?action=upcoming`
- **Description**: Retrieves upcoming events for the authenticated manager.
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
- **URL**: `/api/manager/events/event.php?action=today`
- **Description**: Retrieves events scheduled for today.
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
- **URL**: `/api/manager/events/event.php?action=this_week`
- **Description**: Retrieves events scheduled for the current week.
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
- **URL**: `/api/manager/events/event.php?action=this_month`
- **Description**: Retrieves events scheduled for the current month.
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
- **URL**: `/api/manager/events/event.php?action=by_month`
- **Description**: Retrieves events for a specific month and year.
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
- **URL**: `/api/manager/events/event.php?action=date_range`
- **Description**: Retrieves events within a specified date range.
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
- **URL**: `/api/manager/events/event.php?action=overview`
- **Description**: Retrieves a summary of events for the authenticated manager.
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
- **URL**: `/api/manager/events/event.php?action=search`
- **Description**: Searches events based on a query string and optional date range.
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
- **URL**: `/api/manager/events/event.php?action=with_meet_links`
- **Description**: Retrieves events that have associated meeting links.
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

### 12. Create Event
- **Method**: POST
- **URL**: `/api/manager/events/event.php?action=create`
- **Description**: Creates a new event.
- **Body** (application/json):
  ```json
  {
    "title": "Event Title",
    "date": "2024-12-25",
    "time": "10:00",
    "meet_link": "https://...",
    "description": "Event description"
  }
  ```
- **Response**:
  - **201 Created**:
    ```json
    {
      "success": true,
      "event": { "id": 1, "title": "Event Title", "date": "2024-12-25", ... }
    }
    ```
  - **400 Bad Request**: If `title` or `date` is missing or invalid.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 13. Duplicate Event
- **Method**: POST
- **URL**: `/api/manager/events/event.php?action=duplicate`
- **Description**: Duplicates an existing event with a new date.
- **Body** (application/json):
  ```json
  {
    "event_id": 1,
    "new_date": "2024-12-26"
  }
  ```
- **Response**:
  - **201 Created**:
    ```json
    {
      "success": true,
      "event": { "id": 2, "title": "Event Title", "date": "2024-12-26", ... }
    }
    ```
  - **400 Bad Request**: If `event_id` or `new_date` is missing or invalid.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 14. Update Event
- **Method**: PUT
- **URL**: `/api/manager/events/event.php?action=update&id={eventId}`
- **Description**: Updates an existing event.
- **Query Parameters**:
  - `id` (integer, required): The ID of the event.
- **Body** (application/json):
  ```json
  {
    "title": "Updated Title",
    "date": "2024-12-26",
    "time": "11:00",
    "meet_link": "https://...",
    "description": "Updated description"
  }
  ```
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "message": "Event updated successfully"
    }
    ```
  - **400 Bad Request**: If `id` is invalid or update fails.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 15. Delete Event
- **Method**: DELETE
- **URL**: `/api/manager/events/event.php?action=delete&id={eventId}`
- **Description**: Deletes an event by its ID.
- **Query Parameters**:
  - `id` (integer, required): The ID of the event.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "message": "Event deleted successfully"
    }
    ```
  - **400 Bad Request**: If `id` is invalid or deletion fails.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

## Task Management Endpoints

### 1. List Tasks for Project
- **Method**: GET
- **URL**: `/api/manager/tasks/task.php?action=list&project_id={projectId}`
- **Description**: Retrieves a list of tasks for a specific project.
- **Query Parameters**:
  - `project_id` (integer, required): The ID of the project.
  - `status` (string, optional): Filter tasks by status (e.g., `todo`, `in_progress`, `done`).
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "tasks": [{ "id": 1, "title": "Task Title", "status": "todo", ... }]
    }
    ```
  - **400 Bad Request**: If `project_id` is invalid.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 2. View Task
- **Method**: GET
- **URL**: `/api/manager/tasks/task.php?action=view&project_id={projectId}&id={taskId}`
- **Description**: Retrieves details of a specific task.
- **Query Parameters**:
  - `project_id` (integer, required): The ID of the project.
  - `id` (integer, required): The ID of the task.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "task": { "id": 1, "title": "Task Title", "status": "todo", ... }
    }
    ```
  - **400 Bad Request**: If `project_id` or `id` is invalid.
  - **404 Not Found**: If the task is not found.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 3. Get Task Counts
- **Method**: GET
- **URL**: `/api/manager/tasks/task.php?action=counts&project_id={projectId}`
- **Description**: Retrieves task counts (e.g., by status) for a specific project.
- **Query Parameters**:
  - `project_id` (integer, required): The ID of the project.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "counts": { "todo": 5, "in_progress": 3, "done": 2, ... }
    }
    ```
  - **400 Bad Request**: If `project_id` is invalid.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 4. Get Tasks Overview
- **Method**: GET
- **URL**: `/api/manager/tasks/task.php?action=overview`
- **Description**: Retrieves a summary of tasks for the authenticated manager.
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

### 5. Get Tasks by Status
- **Method**: GET
- **URL**: `/api/manager/tasks/task.php?action=by-status&status={status}`
- **Description**: Retrieves tasks filtered by a specific status.
- **Query Parameters**:
  - `status` (string, required): Task status (e.g., `todo`, `in_progress`, `done`).
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "tasks": [{ "id": 1, "title": "Task Title", "status": "todo", ... }]
    }
    ```
  - **400 Bad Request**: If `status` is missing.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 6. Get Task Assignments
- **Method**: GET
- **URL**: `/api/manager/tasks/task.php?action=assignments&project_id={projectId}&task_id={taskId}`
- **Description**: Retrieves user assignments for a specific task.
- **Query Parameters**:
  - `project_id` (integer, required): The ID of the project.
  - `task_id` (integer, required): The ID of the task.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "assignments": [{ "user_id": 2, "name": "User Name", ... }]
    }
    ```
  - **400 Bad Request**: If `project_id` or `task_id` is invalid.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 7. Get Project Members
- **Method**: GET
- **URL**: `/api/manager/tasks/task.php?action=project-members&project_id={projectId}`
- **Description**: Retrieves members assigned to a specific project.
- **Query Parameters**:
  - `project_id` (integer, required): The ID of the project.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "members": [{ "user_id": 2, "name": "User Name", ... }]
    }
    ```
  - **400 Bad Request**: If `project_id` is invalid.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 8. Create Task
- **Method**: POST
- **URL**: `/api/manager/tasks/task.php?action=create`
- **Description**: Creates a new task in a project.
- **Body** (application/json):
  ```json
  {
    "project_id": 1,
    "title": "New Task",
    "status": "todo"
  }
  ```
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "task": { "id": 1, "title": "New Task", "status": "todo", ... }
    }
    ```
  - **400 Bad Request**: If `project_id` or `title` is invalid or missing.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 9. Update Task
- **Method**: POST or PUT
- **URL**: `/api/manager/tasks/task.php?action=update` (POST) or `/api/manager/tasks/task.php?action=update&project_id={projectId}&id={taskId}` (PUT)
- **Description**: Updates a task's details (title and/or status).
- **Query Parameters** (for PUT):
  - `project_id` (integer, required): The ID of the project.
  - `id` (integer, required): The ID of the task.
- **Body** (application/json):
  ```json
  {
    "project_id": 1,
    "id": 1,
    "title": "Updated Task",
    "status": "in_progress"
  }
  ```
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "task": { "id": 1, "title": "Updated Task", "status": "in_progress", ... }
    }
    ```
  - **400 Bad Request**: If `project_id` or `id` is invalid or update fails.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 10. Update Task Status
- **Method**: POST
- **URL**: `/api/manager/tasks/task.php?action=update-status`
- **Description**: Updates the status of a task.
- **Body** (application/json):
  ```json
  {
    "project_id": 1,
    "id": 1,
    "status": "done"
  }
  ```
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "task": { "id": 1, "title": "Task Title", "status": "done", ... }
    }
    ```
  - **400 Bad Request**: If `project_id`, `id`, or `status` is invalid or missing.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 11. Move Task to Project
- **Method**: POST
- **URL**: `/api/manager/tasks/task.php?action=move`
- **Description**: Moves a task from one project to another.
- **Body** (application/json):
  ```json
  {
    "from_project_id": 1,
    "to_project_id": 2,
    "id": 1
  }
  ```
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "message": "Task moved successfully"
    }
    ```
  - **400 Bad Request**: If `from_project_id`, `to_project_id`, or `id` is invalid.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 12. Assign User to Task
- **Method**: POST
- **URL**: `/api/manager/tasks/task.php?action=assign-user`
- **Description**: Assigns a user to a task.
- **Body** (application/json):
  ```json
  {
    "project_id": 1,
    "task_id": 1,
    "user_id": 2
  }
  ```
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "message": "User assigned successfully",
      "assignments": [{ "user_id": 2, "name": "User Name", ... }]
    }
    ```
  - **400 Bad Request**: If `project_id`, `task_id`, or `user_id` is invalid.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 13. Unassign User from Task
- **Method**: POST
- **URL**: `/api/manager/tasks/task.php?action=unassign-user`
- **Description**: Removes a user assignment from a task.
- **Body** (application/json):
  ```json
  {
    "project_id": 1,
    "task_id": 1,
    "user_id": 2
  }
  ```
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "message": "User unassigned successfully",
      "assignments": [{ ... }]
    }
    ```
  - **400 Bad Request**: If `project_id`, `task_id`, or `user_id` is invalid.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 14. Delete Task
- **Method**: DELETE
- **URL**: `/api/manager/tasks/task.php?action=delete&project_id={projectId}&id={taskId}`
- **Description**: Deletes a task from a project.
- **Query Parameters**:
  - `project_id` (integer, required): The ID of the project.
  - `id` (integer, required): The ID of the task.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "message": "Task deleted successfully"
    }
    ```
  - **400 Bad Request**: If `project_id` or `id` is invalid or deletion fails.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

## Project Management Endpoints

### 1. List Projects
- **Method**: GET
- **URL**: `/api/manager/projects/list.php`
- **Description**: Retrieves a list of all projects for the authenticated manager.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "projects": [{ "id": 1, "title": "Project Title", "description": "Project Description", ... }]
    }
    ```
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 2. View Project
- **Method**: GET
- **URL**: `/api/manager/projects/view.php?id={projectId}`
- **Description**: Retrieves details of a specific project.
- **Query Parameters**:
  - `id` (integer, required): The ID of the project.
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "project": { "id": 1, "title": "Project Title", "description": "Project Description", ... }
    }
    ```
  - **400 Bad Request**: If `id` is invalid.
  - **404 Not Found**: If the project is not found.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 3. Create Project
- **Method**: POST
- **URL**: `/api/manager/projects/create.php`
- **Description**: Creates a new project.
- **Body** (application/json):
  ```json
  {
    "title": "Project Title",
    "description": "Project Description",
    "start_date": "2024-12-01",
    "end_date": "2024-12-31"
  }
  ```
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "project": { "id": 1, "title": "Project Title", "description": "Project Description", ... }
    }
    ```
  - **400 Bad Request**: If required fields are missing or invalid.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 4. Update Project
- **Method**: POST
- **URL**: `/api/manager/projects/update.php`
- **Description**: Updates a project's details.
- **Body** (application/json):
  ```json
  {
    "id": 1,
    "title": "Updated Project Title",
    "description": "Updated Description",
    "start_date": "2024-12-01",
    "end_date": "2024-12-31"
  }
  ```
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "project": { "id": 1, "title": "Updated Project Title", "description": "Updated Description", ... }
    }
    ```
  - **400 Bad Request**: If `id` is invalid or update fails.
  - **401 Unauthorized**: If authentication fails.
  - **500 Server Error**: If a server error occurs.

### 5. Delete Project
- **Method**: POST
- **URL**: `/api/manager/projects/delete.php`
- **Description**: Deletes a project by its ID.
- **Body** (application/json):
  ```json
  {
    "id": 1
  }
  ```
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true
    }
    ```
  - **400 Bad Request**: If `id` is invalid or deletion fails.
  - **401 Unauthorized**: If authentication fails.
  - **405 Method Not Allowed**: If the request method is not POST.
  - **500 Server Error**: If a server error occurs.

### 6. Assign Member to Project
- **Method**: POST
- **URL**: `/api/manager/projects/assign.php`
- **Description**: Assigns a member to a project.
- **Body** (application/json):
  ```json
  {
    "project_id": 1,
    "member_id": 2
  }
  ```
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "project": { ... }
    }
    ```
  - **400 Bad Request**: If `project_id` or `member_id` is invalid.
  - **401 Unauthorized**: If authentication fails.
  - **405 Method Not Allowed**: If the request method is not POST.
  - **500 Server Error**: If a server error occurs.

### 7. Remove Member from Project
- **Method**: POST
- **URL**: `/api/manager/projects/remove.php`
- **Description**: Removes a member from a project.
- **Body** (application/json):
  ```json
  {
    "project_id": 1,
    "member_id": 2
  }
  ```
- **Response**:
  - **200 OK**:
    ```json
    {
      "success": true,
      "project": { ... }
    }
    ```
  - **400 Bad Request**: If `project_id` or `member_id` is invalid.
  - **401 Unauthorized**: If authentication fails.
  - **405 Method Not Allowed**: If the request method is not POST.
  - **500 Server Error**: If a server error occurs.

## Authentication
All endpoints require a valid authentication token with the `manager` role. Include the token in the `Authorization` header.

## Error Responses
- **400 Bad Request**: Invalid input parameters.
- **401 Unauthorized**: Authentication failed or insufficient permissions.
- **404 Not Found**: Resource not found.
- **405 Method Not Allowed**: Unsupported HTTP method.
- **500 Server Error**: Unexpected server error.