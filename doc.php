<?php
// api_documentation.php
// This file generates HTML documentation for the API endpoints

header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h1, h2, h3 {
            color: #2c3e50;
        }
        h1 {
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        h2 {
            margin-top: 30px;
            border-bottom: 1px solid #3498db;
            padding-bottom: 5px;
        }
        .endpoint {
            background: white;
            border-radius: 5px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .method {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 3px;
            color: white;
            display: inline-block;
            margin-right: 10px;
        }
        .method-get { background: #2ecc71; }
        .method-post { background: #3498db; }
        .method-put { background: #f1c40f; }
        .method-delete { background: #e74c3c; }
        .endpoint-url {
            font-family: monospace;
            background: #f8f9fa;
            padding: 5px 10px;
            border-radius: 3px;
            display: inline-block;
        }
        .parameters, .response {
            margin: 15px 0;
            padding-left: 20px;
        }
        .parameters h4, .response h4 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .parameters ul, .response pre {
            margin: 0;
            padding: 10px;
            border-radius: 3px;
        }
        pre {
            background: #2c3e50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #3498db;
            color: white;
        }
    </style>
</head>
<body>
    <h1>API Documentation</h1>
    
    <h2>Events API (Manager)</h2>
    
    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/events/event.php?action=list</span>
        <p><strong>Description:</strong> Retrieve a list of events for the authenticated manager</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>order_by</strong> (optional): Sort field (default: 'date')</li>
                <li><strong>order_direction</strong> (optional): Sort direction (default: 'ASC')</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "events": [...]
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/events/event.php?action=get&id={id}</span>
        <p><strong>Description:</strong> Retrieve a specific event by ID</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>id</strong> (required): Event ID</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "event": {...}
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/events/event.php?action=upcoming&limit={limit}</span>
        <p><strong>Description:</strong> Retrieve upcoming events</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>limit</strong> (optional): Number of events to return (default: 10)</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "events": [...]
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/events/event.php?action={today|this_week|this_month}</span>
        <p><strong>Description:</strong> Retrieve events for today, this week, or this month</p>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "events": [...]
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/events/event.php?action=by_month&year={year}&month={month}</span>
        <p><strong>Description:</strong> Retrieve events for a specific month</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>year</strong> (optional): Year (default: current year)</li>
                <li><strong>month</strong> (optional): Month (default: current month)</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "events": [...]
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/events/event.php?action=date_range&start_date={start_date}&end_date={end_date}</span>
        <p><strong>Description:</strong> Retrieve events within a date range</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>start_date</strong> (required): Start date (YYYY-MM-DD)</li>
                <li><strong>end_date</strong> (required): End date (YYYY-MM-DD)</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "events": [...]
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/events/event.php?action=overview</span>
        <p><strong>Description:</strong> Retrieve events overview</p>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "overview": {...}
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/events/event.php?action=search&query={query}&start_date={start_date}&end_date={end_date}</span>
        <p><strong>Description:</strong> Search events by query</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>query</strong> (required): Search term</li>
                <li><strong>start_date</strong> (optional): Start date (YYYY-MM-DD)</li>
                <li><strong>end_date</strong> (optional): End date (YYYY-MM-DD)</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "events": [...]
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/events/event.php?action=with_meet_links</span>
        <p><strong>Description:</strong> Retrieve events with meeting links</p>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "events": [...]
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-post">POST</span>
        <span class="endpoint-url">/api/manager/events/event.php?action=create</span>
        <p><strong>Description:</strong> Create a new event</p>
        <div class="parameters">
            <h4>Body Parameters:</h4>
            <table>
                <tr><th>Parameter</th><th>Type</th><th>Description</th><th>Required</th></tr>
                <tr><td>title</td><td>string</td><td>Event title</td><td>Yes</td></tr>
                <tr><td>date</td><td>string</td><td>Event date (YYYY-MM-DD)</td><td>Yes</td></tr>
                <tr><td>time</td><td>string</td><td>Event time (HH:MM)</td><td>No</td></tr>
                <tr><td>meet_link</td><td>string</td><td>Meeting link</td><td>No</td></tr>
                <tr><td>description</td><td>string</td><td>Event description</td><td>No</td></tr>
            </table>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "event": {...}
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-post">POST</span>
        <span class="endpoint-url">/api/manager/events/event.php?action=duplicate</span>
        <p><strong>Description:</strong> Duplicate an existing event</p>
        <div class="parameters">
            <h4>Body Parameters:</h4>
            <table>
                <tr><th>Parameter</th><th>Type</th><th>Description</th><th>Required</th></tr>
                <tr><td>event_id</td><td>integer</td><td>Event ID to duplicate</td><td>Yes</td></tr>
                <tr><td>new_date</td><td>string</td><td>New date for duplicated event (YYYY-MM-DD)</td><td>Yes</td></tr>
            </table>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "event": {...}
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-put">PUT</span>
        <span class="endpoint-url">/api/manager/events/event.php?action=update&id={id}</span>
        <p><strong>Description:</strong> Update an existing event</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>id</strong> (required): Event ID</li>
            </ul>
            <h4>Body Parameters:</h4>
            <table>
                <tr><th>Parameter</th><th>Type</th><th>Description</th><th>Required</th></tr>
                <tr><td>title</td><td>string</td><td>Event title</td><td>No</td></tr>
                <tr><td>date</td><td>string</td><td>Event date (YYYY-MM-DD)</td><td>No</td></tr>
                <tr><td>time</td><td>string</td><td>Event time (HH:MM)</td><td>No</td></tr>
                <tr><td>meet_link</td><td>string</td><td>Meeting link</td><td>No</td></tr>
                <tr><td>description</td><td>string</td><td>Event description</td><td>No</td></tr>
            </table>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "message": "Event updated successfully"
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-delete">DELETE</span>
        <span class="endpoint-url">/api/manager/events/event.php?action=delete&id={id}</span>
        <p><strong>Description:</strong> Delete an event</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>id</strong> (required): Event ID</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "message": "Event deleted successfully"
}
            </pre>
        </div>
    </div>

    <h2>Tasks API (Manager)</h2>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/tasks/task.php?action=list&project_id={project_id}</span>
        <p><strong>Description:</strong> Retrieve tasks for a project</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>project_id</strong> (required): Project ID</li>
                <li><strong>status</strong> (optional): Task status (e.g., todo, in_progress, done)</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "tasks": [...]
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/tasks/task.php?action=view&project_id={project_id}&id={id}</span>
        <p><strong>Description:</strong> Retrieve a specific task</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>project_id</strong> (required): Project ID</li>
                <li><strong>id</strong> (required): Task ID</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "task": {...}
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/tasks/task.php?action=counts&project_id={project_id}</span>
        <p><strong>Description:</strong> Retrieve task counts for a project</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>project_id</strong> (required): Project ID</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "counts": {...}
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/tasks/task.php?action=overview</span>
        <p><strong>Description:</strong> Retrieve tasks overview</p>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "overview": {...}
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/tasks/task.php?action=by-status&status={status}</span>
        <p><strong>Description:</strong> Retrieve tasks by status</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>status</strong> (required): Task status (e.g., todo, in_progress, done)</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "tasks": [...]
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/tasks/task.php?action=assignments&project_id={project_id}&task_id={task_id}</span>
        <p><strong>Description:</strong> Retrieve task assignments</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>project_id</strong> (required): Project ID</li>
                <li><strong>task_id</strong> (required): Task ID</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "assignments": [...]
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/tasks/task.php?action=project-members&project_id={project_id}</span>
        <p><strong>Description:</strong> Retrieve project members</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>project_id</strong> (required): Project ID</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "members": [...]
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-post">POST</span>
        <span class="endpoint-url">/api/manager/tasks/task.php?action=create</span>
        <p><strong>Description:</strong> Create a new task</p>
        <div class="parameters">
            <h4>Body Parameters:</h4>
            <table>
                <tr><th>Parameter</th><th>Type</th><th>Description</th><th>Required</th></tr>
                <tr><td>project_id</td><td>integer</td><td>Project ID</td><td>Yes</td></tr>
                <tr><td>title</td><td>string</td><td>Task title</td><td>Yes</td></tr>
                <tr><td>status</td><td>string</td><td>Task status (default: todo)</td><td>No</td></tr>
            </table>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "task": {...}
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-post">POST</span>
        <span class="endpoint-url">/api/manager/tasks/task.php?action=update</span>
        <p><strong>Description:</strong> Update a task</p>
        <div class="parameters">
            <h4>Body Parameters:</h4>
            <table>
                <tr><th>Parameter</th><th>Type</th><th>Description</th><th>Required</th></tr>
                <tr><td>project_id</td><td>integer</td><td>Project ID</td><td>Yes</td></tr>
                <tr><td>id</td><td>integer</td><td>Task ID</td><td>Yes</td></tr>
                <tr><td>title</td><td>string</td><td>Task title</td><td>No</td></tr>
                <tr><td>status</td><td>string</td><td>Task status</td><td>No</td></tr>
            </table>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "task": {...}
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-post">POST</span>
        <span class="endpoint-url">/api/manager/tasks/task.php?action=update-status</span>
        <p><strong>Description:</strong> Update task status</p>
        <div class="parameters">
            <h4>Body Parameters:</h4>
            <table>
                <tr><th>Parameter</th><th>Type</th><th>Description</th><th>Required</th></tr>
                <tr><td>project_id</td><td>integer</td><td>Project ID</td><td>Yes</td></tr>
                <tr><td>id</td><td>integer</td><td>Task ID</td><td>Yes</td></tr>
                <tr><td>status</td><td>string</td><td>Task status</td><td>Yes</td></tr>
            </table>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "task": {...}
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-post">POST</span>
        <span class="endpoint-url">/api/manager/tasks/task.php?action=move</span>
        <p><strong>Description:</strong> Move a task to another project</p>
        <div class="parameters">
            <h4>Body Parameters:</h4>
            <table>
                <tr><th>Parameter</th><th>Type</th><th>Description</th><th>Required</th></tr>
                <tr><td>from_project_id</td><td>integer</td><td>Source Project ID</td><td>Yes</td></tr>
                <tr><td>to_project_id</td><td>integer</td><td>Target Project ID</td><td>Yes</td></tr>
                <tr><td>id</td><td>integer</td><td>Task ID</td><td>Yes</td></tr>
            </table>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "message": "Task moved successfully"
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-post">POST</span>
        <span class="endpoint-url">/api/manager/tasks/task.php?action=assign-user</span>
        <p><strong>Description:</strong> Assign a user to a task</p>
        <div class="parameters">
            <h4>Body Parameters:</h4>
            <table>
                <tr><th>Parameter</th><th>Type</th><th>Description</th><th>Required</th></tr>
                <tr><td>project_id</td><td>integer</td><td>Project ID</td><td>Yes</td></tr>
                <tr><td>task_id</td><td>integer</td><td>Task ID</td><td>Yes</td></tr>
                <tr><td>user_id</td><td>integer</td><td>User ID</td><td>Yes</td></tr>
            </table>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "message": "User assigned successfully",
    "assignments": [...]
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-post">POST</span>
        <span class="endpoint-url">/api/manager/tasks/task.php?action=unassign-user</span>
        <p><strong>Description:</strong> Unassign a user from a task</p>
        <div class="parameters">
            <h4>Body Parameters:</h4>
            <table>
                <tr><th>Parameter</th><th>Type</th><th>Description</th><th>Required</th></tr>
                <tr><td>project_id</td><td>integer</td><td>Project ID</td><td>Yes</td></tr>
                <tr><td>task_id</td><td>integer</td><td>Task ID</td><td>Yes</td></tr>
                <tr><td>user_id</td><td>integer</td><td>User ID</td><td>Yes</td></tr>
            </table>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "message": "User unassigned successfully",
    "assignments": [...]
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-put">PUT</span>
        <span class="endpoint-url">/api/manager/tasks/task.php?action=update&project_id={project_id}&id={id}</span>
        <p><strong>Description:</strong> Update a task (alternative method)</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>project_id</strong> (required): Project ID</li>
                <li><strong>id</strong> (required): Task ID</li>
            </ul>
            <h4>Body Parameters:</h4>
            <table>
                <tr><th>Parameter</th><th>Type</th><th>Description</th><th>Required</th></tr>
                <tr><td>title</td><td>string</td><td>Task title</td><td>No</td></tr>
                <tr><td>status</td><td>string</td><td>Task status</td><td>No</td></tr>
            </table>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "task": {...}
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-delete">DELETE</span>
        <span class="endpoint-url">/api/manager/tasks/task.php?action=delete&project_id={project_id}&id={id}</span>
        <p><strong>Description:</strong> Delete a task</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>project_id</strong> (required): Project ID</li>
                <li><strong>id</strong> (required): Task ID</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "message": "Task deleted successfully"
}
            </pre>
        </div>
    </div>

    <h2>Projects API (Member)</h2>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/projects.php?action=list</span>
        <p><strong>Description:</strong> Retrieve projects for a member</p>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "tasks": [...]
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/projects.php?action=view&id={id}</span>
        <p><strong>Description:</strong> Retrieve a specific project</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>id</strong> (required): Project ID</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "task": {...}
}
            </pre>
        </div>
    </div>

    <h2>Tasks API (Member)</h2>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/tasks/task.php?action=list&id={project_id}</span>
        <p><strong>Description:</strong> Retrieve tasks assigned to a member for a project</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>id</strong> (required): Project ID</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "tasks": [...]
}
            </pre>
        </div>
    </div>

    <h2>Notices API (Coordinator/Manager)</h2>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/coordinator/supervisors/notice.php?action=list</span>
        <p><strong>Description:</strong> Retrieve all notices</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>status</strong> (optional): Notice status (e.g., active)</li>
                <li><strong>priority</strong> (optional): Notice priority (e.g., high)</li>
                <li><strong>is_public</strong> (optional): Public status (1 or 0)</li>
                <li><strong>page</strong> (optional): Page number</li>
                <li><strong>limit</strong> (optional): Items per page</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "data": [...]
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/coordinator/supervisors/notice.php?action=view&id={id}</span>
        <p><strong>Description:</strong> Retrieve a specific notice</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>id</strong> (required): Notice ID</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "notice": {...}
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-post">POST</span>
        <span class="endpoint-url">/api/coordinator/supervisors/notice.php?action=create</span>
        <p><strong>Description:</strong> Create a new notice (Coordinator only)</p>
        <div class="parameters">
            <h4>Body Parameters:</h4>
            <table>
                <tr><th>Parameter</th><th>Type</th><th>Description</th><th>Required</th></tr>
                <tr><td>title</td><td>string</td><td>Notice title</td><td>Yes</td></tr>
                <tr><td>content</td><td>string</td><td>Notice content</td><td>Yes</td></tr>
                <tr><td>priority</td><td>string</td><td>Priority (default: normal)</td><td>No</td></tr>
                <tr><td>status</td><td>string</td><td>Status (default: active)</td><td>No</td></tr>
                <tr><td>expires_at</td><td>string</td><td>Expiration date (YYYY-MM-DD HH:MM:SS)</td><td>No</td></tr>
                <tr><td>is_public</td><td>boolean</td><td>Public status</td><td>No</td></tr>
            </table>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "notice": {...}
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-post">POST</span>
        <span class="endpoint-url">/api/coordinator/supervisors/notice.php?action=update</span>
        <p><strong>Description:</strong> Update a notice (Coordinator only)</p>
        <div class="parameters">
            <h4>Body Parameters:</h4>
            <table>
                <tr><th>Parameter</th><th>Type</th><th>Description</th><th>Required</th></tr>
                <tr><td>id</td><td>integer</td><td>Notice ID</td><td>Yes</td></tr>
                <tr><td>title</td><td>string</td><td>Notice title</td><td>No</td></tr>
                <tr><td>content</td><td>string</td><td>Notice content</td><td>No</td></tr>
                <tr><td>priority</td><td>string</td><td>Priority</td><td>No</td></tr>
                <tr><td>status</td><td>string</td><td>Status</td><td>No</td></tr>
                <tr><td>expires_at</td><td>string</td><td>Expiration date</td><td>No</td></tr>
                <tr><td>is_public</td><td>boolean</td><td>Public status</td><td>No</td></tr>
            </table>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "notice": {...}
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-put">PUT</span>
        <span class="endpoint-url">/api/coordinator/supervisors/notice.php?action=update&id={id}</span>
        <p><strong>Description:</strong> Update a notice (Coordinator only, alternative method)</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>id</strong> (required): Notice ID</li>
            </ul>
            <h4>Body Parameters:</h4>
            <table>
                <tr><th>Parameter</th><th>Type</th><th>Description</th><th>Required</th></tr>
                <tr><td>title</td><td>string</td><td>Notice title</td><td>No</td></tr>
                <tr><td>content</td><td>string</td><td>Notice content</td><td>No</td></tr>
                <tr><td>priority</td><td>string</td><td>Priority</td><td>No</td></tr>
                <tr><td>status</td><td>string</td><td>

Status</td><td>No</td></tr>
                <tr><td>expires_at</td><td>string</td><td>Expiration date</td><td>No</td></tr>
                <tr><td>is_public</td><td>boolean</td><td>Public status</td><td>No</td></tr>
            </table>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "notice": {...}
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-delete">DELETE</span>
        <span class="endpoint-url">/api/coordinator/supervisors/notice.php?action=delete&id={id}</span>
        <p><strong>Description:</strong> Delete a notice (Coordinator only)</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>id</strong> (required): Notice ID</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "message": "Notice deleted successfully"
}
            </pre>
        </div>
    </div>

    <h2>Supervisors API (Coordinator/Manager)</h2>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/supervisors.php?action=list</span>
        <p><strong>Description:</strong> Retrieve all supervisors</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>type</strong> (optional): Supervisor type</li>
                <li><strong>faculty_name</strong> (optional): Faculty name</li>
                <li><strong>department_name</strong> (optional): Department name</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "supervisors": [...]
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-get">GET</span>
        <span class="endpoint-url">/api/manager/supervisors.php?action=view&id={id}</span>
        <p><strong>Description:</strong> Retrieve a specific supervisor</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>id</strong> (required): Supervisor ID</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "supervisor": {...}
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-post">POST</span>
        <span class="endpoint-url">/api/manager/supervisors.php?action=create</span>
        <p><strong>Description:</strong> Create a new supervisor</p>
        <div class="parameters">
            <h4>Body Parameters:</h4>
            <table>
                <tr><th>Parameter</th><th>Type</th><th>Description</th><th>Required</th></tr>
                <tr><td>full_name</td><td>string</td><td>Supervisor's full name</td><td>Yes</td></tr>
                <tr><td>email</td><td>string</td><td>Supervisor's email</td><td>Yes</td></tr>
                <tr><td>contact</td><td>string</td><td>Contact information</td><td>Yes</td></tr>
                <tr><td>type</td><td>string</td><td>Supervisor type</td><td>Yes</td></tr>
                <tr><td>faculty_name</td><td>string</td><td>Faculty name</td><td>Yes</td></tr>
                <tr><td>department_name</td><td>string</td><td>Department name</td><td>Yes</td></tr>
                <tr><td>about</td><td>string</td><td>Description</td><td>No</td></tr>
            </table>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "supervisor": {...}
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-post">POST</span>
        <span class="endpoint-url">/api/manager/supervisors.php?action=update</span>
        <p><strong>Description:</strong> Update a supervisor</p>
        <div class="parameters">
            <h4>Body Parameters:</h4>
            <table>
                <tr><th>Parameter</th><th>Type</th><th>Description</th><th>Required</th></tr>
                <tr><td>id</td><td>integer</td><td>Supervisor ID</td><td>Yes</td></tr>
                <tr><td>full_name</td><td>string</td><td>Supervisor's full name</td><td>No</td></tr>
                <tr><td>email</td><td>string</td><td>Supervisor's email</td><td>No</td></tr>
                <tr><td>contact</td><td>string</td><td>Contact information</td><td>No</td></tr>
                <tr><td>type</td><td>string</td><td>Supervisor type</td><td>No</td></tr>
                <tr><td>faculty_name</td><td>string</td><td>Faculty name</td><td>No</td></tr>
                <tr><td>department_name</td><td>string</td><td>Department name</td><td>No</td></tr>
                <tr><td>about</td><td>string</td><td>Description</td><td>No</td></tr>
            </table>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "supervisor": {...}
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-post">POST</span>
        <span class="endpoint-url">/api/manager/supervisors.php?action=assign-to-project</span>
        <p><strong>Description:</strong> Assign a supervisor to a project</p>
        <div class="parameters">
            <h4>Body Parameters:</h4>
            <table>
                <tr><th>Parameter</th><th>Type</th><th>Description</th><th>Required</th></tr>
                <tr><td>project_id</td HDL><td>integer</td><td>Project ID</td><td>Yes</td></tr>
                <tr><td>supervisor_id</td><td>integer</td><td>Supervisor ID</td><td>Yes</td></tr>
            </table>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "message": "Supervisor assigned successfully",
    "supervisors": [...]
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-post">POST</span>
        <span class="endpoint-url">/api/manager/supervisors.php?action=unassign-from-project</span>
        <p><strong>Description:</strong> Unassign a supervisor from a project</p>
        <div class="parameters">
            <h4>Body Parameters:</h4>
            <table>
                <tr><th>Parameter</th><th>Type</th><th>Description</th><th>Required</th></tr>
                <tr><td>project_id</td><td>integer</td><td>Project ID</td><td>Yes</td></tr>
                <tr><td>supervisor_id</td><td>integer</td><td>Supervisor ID</td><td>Yes</td></tr>
            </table>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "message": "Supervisor unassigned successfully",
    "supervisors": [...]
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-put">PUT</span>
        <span class="endpoint-url">/api/manager/supervisors.php?action=update&id={id}</span>
        <p><strong>Description:</strong> Update a supervisor (alternative method)</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>id</strong> (required): Supervisor ID</li>
            </ul>
            <h4>Body Parameters:</h4>
            <table>
                <tr><th>Parameter</th><th>Type</th><th>Description</th><th>Required</th></tr>
                <tr><td>full_name</td><td>string</td><td>Supervisor's full name</td><td>No</td></tr>
                <tr><td>email</td><td>string</td><td>Supervisor's email</td><td>No</td></tr>
                <tr><td>contact</td><td>string</td><td>Contact information</td><td>No</td></tr>
                <tr><td>type</td><td>string</td><td>Supervisor type</td><td>No</td></tr>
                <tr><td>faculty_name</td><td>string</td><td>Faculty name</td><td>No</td></tr>
                <tr><td>department_name</td><td>string</td><td>Department name</td><td>No</td></tr>
                <tr><td>about</td><td>string</td><td>Description</td><td>No</td></tr>
            </table>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "supervisor": {...}
}
            </pre>
        </div>
    </div>

    <div class="endpoint">
        <span class="method method-delete">DELETE</span>
        <span class="endpoint-url">/api/manager/supervisors.php?action=delete&id={id}</span>
        <p><strong>Description:</strong> Delete a supervisor</p>
        <div class="parameters">
            <h4>Query Parameters:</h4>
            <ul>
                <li><strong>id</strong> (required): Supervisor ID</li>
            </ul>
        </div>
        <div class="response">
            <h4>Response:</h4>
            <pre>
{
    "success": true,
    "message": "Supervisor deleted successfully"
}
            </pre>
        </div>
    </div>

</body>
</html>