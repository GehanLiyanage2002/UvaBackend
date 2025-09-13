<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMS API Documentation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            background: #f8fafc;
            color: #334155;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
            border-left: 4px solid #3b82f6;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #1e293b;
        }

        .header p {
            color: #64748b;
            font-size: 16px;
        }

        .auth-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
        }

        .auth-controls {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .auth-controls input {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 14px;
        }

        .auth-controls input.active {
            border-color: #10b981;
            background: #f0fdf4;
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .endpoint-grid {
            display: grid;
            gap: 20px;
        }

        .endpoint-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .endpoint-header {
            padding: 16px 20px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: between;
            align-items: center;
            gap: 12px;
        }

        .endpoint-title {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            flex: 1;
        }

        .method-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .get { background: #dbeafe; color: #1d4ed8; }
        .post { background: #dcfce7; color: #166534; }
        .put { background: #fef3c7; color: #92400e; }
        .delete { background: #fecaca; color: #991b1b; }

        .endpoint-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 6px;
            color: #374151;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .json-editor {
            width: 100%;
            min-height: 120px;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 13px;
            background: #1f2937;
            color: #f9fafb;
            resize: vertical;
        }

        .json-editor:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .controls {
            display: flex;
            gap: 8px;
            margin-top: 16px;
        }

        .response-section {
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
        }

        .response-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .response-header h4 {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-success { background: #dcfce7; color: #166534; }
        .status-error { background: #fecaca; color: #991b1b; }

        .response-body {
            background: #1f2937;
            color: #f9fafb;
            padding: 12px;
            border-radius: 6px;
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 13px;
            white-space: pre-wrap;
            max-height: 300px;
            overflow-y: auto;
        }

        .hidden {
            display: none;
        }

        .loading {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .spinner {
            width: 14px;
            height: 14px;
            border: 2px solid #e5e7eb;
            border-top: 2px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 16px;
            border-radius: 6px;
            color: white;
            font-weight: 500;
            z-index: 1000;
            font-size: 14px;
        }

        .notification.success {
            background: #10b981;
        }

        .notification.error {
            background: #ef4444;
        }

        @media (max-width: 768px) {
            .container {
                padding: 16px;
            }
            
            .auth-controls {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PMS API Documentation</h1>
            <p>Project Management System - API Testing Interface</p>
        </div>

        <div class="auth-section">
            <div class="auth-controls">
                <input type="text" id="tokenDisplay" placeholder="Bearer token will appear here after login..." readonly>
                <button class="btn btn-primary" onclick="performLogin()">Login & Get Token</button>
            </div>
        </div>

        <div class="endpoint-grid">
            <!-- Login -->
            <div class="endpoint-card">
                <div class="endpoint-header">
                    <h3 class="endpoint-title">🔐 User Login</h3>
                    <span class="method-badge post">POST</span>
                </div>
                <div class="endpoint-body">
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" class="form-input" value="http://localhost/uwu_pms_backend-main/api/login.php" readonly>
                    </div>
                    <div class="form-group">
                        <label>Request Body</label>
                        <textarea class="json-editor" id="loginPayload">{
  "email": "udara@std.uwu.ac.lk",
  "password": "12345678Aa#"
}</textarea>
                    </div>
                    <div class="controls">
                        <button class="btn btn-primary" onclick="testEndpoint('login')">Send Request</button>
                        <button class="btn btn-secondary" onclick="clearResponse('login')">Clear</button>
                    </div>
                    <div class="response-section hidden" id="loginResponse">
                        <div class="response-header">
                            <h4>Response</h4>
                            <span class="status-badge" id="loginStatus"></span>
                        </div>
                        <div class="response-body" id="loginBody"></div>
                    </div>
                </div>
            </div>

            <!-- Projects Section -->
            <div class="endpoint-card">
                <div class="endpoint-header">
                    <h3 class="endpoint-title">📋 List Projects</h3>
                    <span class="method-badge get">GET</span>
                </div>
                <div class="endpoint-body">
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" class="form-input" value="http://localhost/uwu_pms_backend-main/api/manager/projects/list.php" readonly>
                    </div>
                    <div class="controls">
                        <button class="btn btn-primary" onclick="testEndpoint('projectList')">Send Request</button>
                        <button class="btn btn-secondary" onclick="clearResponse('projectList')">Clear</button>
                    </div>
                    <div class="response-section hidden" id="projectListResponse">
                        <div class="response-header">
                            <h4>Response</h4>
                            <span class="status-badge" id="projectListStatus"></span>
                        </div>
                        <div class="response-body" id="projectListBody"></div>
                    </div>
                </div>
            </div>

            <div class="endpoint-card">
                <div class="endpoint-header">
                    <h3 class="endpoint-title">➕ Create Project</h3>
                    <span class="method-badge post">POST</span>
                </div>
                <div class="endpoint-body">
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" class="form-input" value="http://localhost/uwu_pms_backend-main/api/manager/projects/create.php" readonly>
                    </div>
                    <div class="form-group">
                        <label>Request Body</label>
                        <textarea class="json-editor" id="createProjectPayload">{
  "title": "New Project",
  "description": "Project description here",
  "start_date": "2025-08-12",
  "end_date": "2025-08-20"
}</textarea>
                    </div>
                    <div class="controls">
                        <button class="btn btn-primary" onclick="testEndpoint('createProject')">Send Request</button>
                        <button class="btn btn-secondary" onclick="clearResponse('createProject')">Clear</button>
                    </div>
                    <div class="response-section hidden" id="createProjectResponse">
                        <div class="response-header">
                            <h4>Response</h4>
                            <span class="status-badge" id="createProjectStatus"></span>
                        </div>
                        <div class="response-body" id="createProjectBody"></div>
                    </div>
                </div>
            </div>

            <div class="endpoint-card">
                <div class="endpoint-header">
                    <h3 class="endpoint-title">👁️ View Project</h3>
                    <span class="method-badge get">GET</span>
                </div>
                <div class="endpoint-body">
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" class="form-input" id="viewProjectUrl" value="http://localhost/uwu_pms_backend-main/api/manager/projects/view.php?id=1">
                    </div>
                    <div class="controls">
                        <button class="btn btn-primary" onclick="testEndpoint('viewProject')">Send Request</button>
                        <button class="btn btn-secondary" onclick="clearResponse('viewProject')">Clear</button>
                    </div>
                    <div class="response-section hidden" id="viewProjectResponse">
                        <div class="response-header">
                            <h4>Response</h4>
                            <span class="status-badge" id="viewProjectStatus"></span>
                        </div>
                        <div class="response-body" id="viewProjectBody"></div>
                    </div>
                </div>
            </div>

            <div class="endpoint-card">
                <div class="endpoint-header">
                    <h3 class="endpoint-title">✏️ Update Project</h3>
                    <span class="method-badge post">POST</span>
                </div>
                <div class="endpoint-body">
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" class="form-input" value="http://localhost/uwu_pms_backend-main/api/manager/projects/update.php" readonly>
                    </div>
                    <div class="form-group">
                        <label>Request Body</label>
                        <textarea class="json-editor" id="updateProjectPayload">{
  "id": 1,
  "title": "Updated Project Title",
  "description": "Updated description",
  "start_date": "2025-08-10",
  "end_date": "2025-08-25"
}</textarea>
                    </div>
                    <div class="controls">
                        <button class="btn btn-primary" onclick="testEndpoint('updateProject')">Send Request</button>
                        <button class="btn btn-secondary" onclick="clearResponse('updateProject')">Clear</button>
                    </div>
                    <div class="response-section hidden" id="updateProjectResponse">
                        <div class="response-header">
                            <h4>Response</h4>
                            <span class="status-badge" id="updateProjectStatus"></span>
                        </div>
                        <div class="response-body" id="updateProjectBody"></div>
                    </div>
                </div>
            </div>

            <div class="endpoint-card">
                <div class="endpoint-header">
                    <h3 class="endpoint-title">🗑️ Delete Project</h3>
                    <span class="method-badge delete">GET</span>
                </div>
                <div class="endpoint-body">
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" class="form-input" value="http://localhost/uwu_pms_backend-main/api/manager/projects/delete.php" readonly>
                    </div>
                    <div class="form-group">
                        <label>Request Body</label>
                        <textarea class="json-editor" id="deleteProjectPayload">{
  "id": 1
}</textarea>
                    </div>
                    <div class="controls">
                        <button class="btn btn-primary" onclick="testEndpoint('deleteProject')">Send Request</button>
                        <button class="btn btn-secondary" onclick="clearResponse('deleteProject')">Clear</button>
                    </div>
                    <div class="response-section hidden" id="deleteProjectResponse">
                        <div class="response-header">
                            <h4>Response</h4>
                            <span class="status-badge" id="deleteProjectStatus"></span>
                        </div>
                        <div class="response-body" id="deleteProjectBody"></div>
                    </div>
                </div>
            </div>

            <div class="endpoint-card">
                <div class="endpoint-header">
                    <h3 class="endpoint-title">👤 Assign Member To Project</h3>
                    <span class="method-badge post">POST</span>
                </div>
                <div class="endpoint-body">
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" class="form-input" value="http://localhost/uwu_pms_backend-main/api/manager/projects/assign.php" readonly>
                    </div>
                    <div class="form-group">
                        <label>Request Body</label>
                        <textarea class="json-editor" id="assignMemberPayload">{
  "project_id": 1,
  "member_id": 2
}</textarea>
                    </div>
                    <div class="controls">
                        <button class="btn btn-primary" onclick="testEndpoint('assignMember')">Send Request</button>
                        <button class="btn btn-secondary" onclick="clearResponse('assignMember')">Clear</button>
                    </div>
                    <div class="response-section hidden" id="assignMemberResponse">
                        <div class="response-header">
                            <h4>Response</h4>
                            <span class="status-badge" id="assignMemberStatus"></span>
                        </div>
                        <div class="response-body" id="assignMemberBody"></div>
                    </div>
                </div>
            </div>

            <div class="endpoint-card">
                <div class="endpoint-header">
                    <h3 class="endpoint-title">❌ Remove Member From Project</h3>
                    <span class="method-badge post">POST</span>
                </div>
                <div class="endpoint-body">
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" class="form-input" value="http://localhost/uwu_pms_backend-main/api/manager/projects/remove.php" readonly>
                    </div>
                    <div class="form-group">
                        <label>Request Body</label>
                        <textarea class="json-editor" id="removeMemberPayload">{
  "project_id": 1,
  "member_id": 2
}</textarea>
                    </div>
                    <div class="controls">
                        <button class="btn btn-primary" onclick="testEndpoint('removeMember')">Send Request</button>
                        <button class="btn btn-secondary" onclick="clearResponse('removeMember')">Clear</button>
                    </div>
                    <div class="response-section hidden" id="removeMemberResponse">
                        <div class="response-header">
                            <h4>Response</h4>
                            <span class="status-badge" id="removeMemberStatus"></span>
                        </div>
                        <div class="response-body" id="removeMemberBody"></div>
                    </div>
                </div>
            </div>

            <!-- Tasks Section -->
            <div class="endpoint-card">
                <div class="endpoint-header">
                    <h3 class="endpoint-title">📝 List Tasks</h3>
                    <span class="method-badge get">GET</span>
                </div>
                <div class="endpoint-body">
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" class="form-input" id="listTasksUrl" value="http://localhost/uwu_pms_backend-main/api/manager/tasks/task.php?action=list&project_id=1">
                    </div>
                    <div class="controls">
                        <button class="btn btn-primary" onclick="testEndpoint('listTasks')">Send Request</button>
                        <button class="btn btn-secondary" onclick="clearResponse('listTasks')">Clear</button>
                    </div>
                    <div class="response-section hidden" id="listTasksResponse">
                        <div class="response-header">
                            <h4>Response</h4>
                            <span class="status-badge" id="listTasksStatus"></span>
                        </div>
                        <div class="response-body" id="listTasksBody"></div>
                    </div>
                </div>
            </div>

            <div class="endpoint-card">
                <div class="endpoint-header">
                    <h3 class="endpoint-title">➕ Create Task</h3>
                    <span class="method-badge post">POST</span>
                </div>
                <div class="endpoint-body">
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" class="form-input" value="http://localhost/uwu_pms_backend-main/api/manager/tasks/task.php?action=create" readonly>
                    </div>
                    <div class="form-group">
                        <label>Request Body</label>
                        <textarea class="json-editor" id="createTaskPayload">{
  "project_id": 1,
  "title": "New Task",
  "status": "todo"
}</textarea>
                    </div>
                    <div class="controls">
                        <button class="btn btn-primary" onclick="testEndpoint('createTask')">Send Request</button>
                        <button class="btn btn-secondary" onclick="clearResponse('createTask')">Clear</button>
                    </div>
                    <div class="response-section hidden" id="createTaskResponse">
                        <div class="response-header">
                            <h4>Response</h4>
                            <span class="status-badge" id="createTaskStatus"></span>
                        </div>
                        <div class="response-body" id="createTaskBody"></div>
                    </div>
                </div>
            </div>

            <div class="endpoint-card">
                <div class="endpoint-header">
                    <h3 class="endpoint-title">👁️ View Task</h3>
                    <span class="method-badge get">GET</span>
                </div>
                <div class="endpoint-body">
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" class="form-input" id="viewTaskUrl" value="http://localhost/uwu_pms_backend-main/api/manager/tasks/task.php?action=view&project_id=1&id=1">
                    </div>
                    <div class="controls">
                        <button class="btn btn-primary" onclick="testEndpoint('viewTask')">Send Request</button>
                        <button class="btn btn-secondary" onclick="clearResponse('viewTask')">Clear</button>
                    </div>
                    <div class="response-section hidden" id="viewTaskResponse">
                        <div class="response-header">
                            <h4>Response</h4>
                            <span class="status-badge" id="viewTaskStatus"></span>
                        </div>
                        <div class="response-body" id="viewTaskBody"></div>
                    </div>
                </div>
            </div>

            <div class="endpoint-card">
                <div class="endpoint-header">
                    <h3 class="endpoint-title">✏️ Update Task</h3>
                    <span class="method-badge post">POST</span>
                </div>
                <div class="endpoint-body">
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" class="form-input" value="http://localhost/uwu_pms_backend-main/api/manager/tasks/task.php?action=update" readonly>
                    </div>
                    <div class="form-group">
                        <label>Request Body</label>
                        <textarea class="json-editor" id="updateTaskPayload">{
  "project_id": 1,
  "id": 1,
  "title": "Updated Task Title",
  "status": "in_progress"
}</textarea>
                    </div>
                    <div class="controls">
                        <button class="btn btn-primary" onclick="testEndpoint('updateTask')">Send Request</button>
                        <button class="btn btn-secondary" onclick="clearResponse('updateTask')">Clear</button>
                    </div>
                    <div class="response-section hidden" id="updateTaskResponse">
                        <div class="response-header">
                            <h4>Response</h4>
                            <span class="status-badge" id="updateTaskStatus"></span>
                        </div>
                        <div class="response-body" id="updateTaskBody"></div>
                    </div>
                </div>
            </div>

            <div class="endpoint-card">
                <div class="endpoint-header">
                    <h3 class="endpoint-title">🔄 Update Task Status</h3>
                    <span class="method-badge post">POST</span>
                </div>
                <div class="endpoint-body">
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" class="form-input" value="http://localhost/uwu_pms_backend-main/api/manager/tasks/task.php?action=update-status" readonly>
                    </div>
                    <div class="form-group">
                        <label>Request Body</label>
                        <textarea class="json-editor" id="updateStatusPayload">{
  "project_id": 1,
  "id": 1,
  "status": "done"
}</textarea>
                    </div>
                    <div class="controls">
                        <button class="btn btn-primary" onclick="testEndpoint('updateStatus')">Send Request</button>
                        <button class="btn btn-secondary" onclick="clearResponse('updateStatus')">Clear</button>
                    </div>
                    <div class="response-section hidden" id="updateStatusResponse">
                        <div class="response-header">
                            <h4>Response</h4>
                            <span class="status-badge" id="updateStatusStatus"></span>
                        </div>
                        <div class="response-body" id="updateStatusBody"></div>
                    </div>
                </div>
            </div>

            <div class="endpoint-card">
                <div class="endpoint-header">
                    <h3 class="endpoint-title">🗑️ Delete Task</h3>
                    <span class="method-badge delete">DELETE</span>
                </div>
                <div class="endpoint-body">
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" class="form-input" id="deleteTaskUrl" value="http://localhost/uwu_pms_backend-main/api/manager/tasks/task.php?action=delete&project_id=1&id=1">
                    </div>
                    <div class="controls">
                        <button class="btn btn-primary" onclick="testEndpoint('deleteTask')">Send Request</button>
                        <button class="btn btn-secondary" onclick="clearResponse('deleteTask')">Clear</button>
                    </div>
                    <div class="response-section hidden" id="deleteTaskResponse">
                        <div class="response-header">
                            <h4>Response</h4>
                            <span class="status-badge" id="deleteTaskStatus"></span>
                        </div>
                        <div class="response-body" id="deleteTaskBody"></div>
                    </div>
                </div>
            </div>

            <div class="endpoint-card">
                <div class="endpoint-header">
                    <h3 class="endpoint-title">📊 Task Overview</h3>
                    <span class="method-badge get">GET</span>
                </div>
                <div class="endpoint-body">
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" class="form-input" value="http://localhost/uwu_pms_backend-main/api/manager/tasks/task.php?action=overview" readonly>
                    </div>
                    <div class="controls">
                        <button class="btn btn-primary" onclick="testEndpoint('taskOverview')">Send Request</button>
                        <button class="btn btn-secondary" onclick="clearResponse('taskOverview')">Clear</button>
                    </div>
                    <div class="response-section hidden" id="taskOverviewResponse">
                        <div class="response-header">
                            <h4>Response</h4>
                            <span class="status-badge" id="taskOverviewStatus"></span>
                        </div>
                        <div class="response-body" id="taskOverviewBody"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let authToken = '';

        const endpoints = {
            login: { url: 'http://localhost/uwu_pms_backend-main/api/login.php', method: 'POST', requiresAuth: false },
            projectList: { url: 'http://localhost/uwu_pms_backend-main/api/manager/projects/list.php', method: 'GET', requiresAuth: true },
            createProject: { url: 'http://localhost/uwu_pms_backend-main/api/manager/projects/create.php', method: 'POST', requiresAuth: true },
            viewProject: { method: 'GET', requiresAuth: true },
            updateProject: { url: 'http://localhost/uwu_pms_backend-main/api/manager/projects/update.php', method: 'POST', requiresAuth: true },
            deleteProject: { url: 'http://localhost/uwu_pms_backend-main/api/manager/projects/delete.php', method: 'GET', requiresAuth: true },
            assignMember: { url: 'http://localhost/uwu_pms_backend-main/api/manager/projects/assign.php', method: 'POST', requiresAuth: true },
            removeMember: { url: 'http://localhost/uwu_pms_backend-main/api/manager/projects/remove.php', method: 'POST', requiresAuth: true },
            listTasks: { method: 'GET', requiresAuth: true },
            createTask: { url: 'http://localhost/uwu_pms_backend-main/api/manager/tasks/task.php?action=create', method: 'POST', requiresAuth: true },
            viewTask: { method: 'GET', requiresAuth: true },
            updateTask: { url: 'http://localhost/uwu_pms_backend-main/api/manager/tasks/task.php?action=update', method: 'POST', requiresAuth: true },
            updateStatus: { url: 'http://localhost/uwu_pms_backend-main/api/manager/tasks/task.php?action=update-status', method: 'POST', requiresAuth: true },
            deleteTask: { method: 'DELETE', requiresAuth: true },
            taskOverview: { url: 'http://localhost/uwu_pms_backend-main/api/manager/tasks/task.php?action=overview', method: 'GET', requiresAuth: true }
        };

        async function performLogin() {
            const payload = document.getElementById('loginPayload').value;
            
            try {
                const response = await fetch(endpoints.login.url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: payload
                });

                const data = await response.json();
                
                if (data.token) {
                    authToken = data.token;
                    document.getElementById('tokenDisplay').value = `Bearer ${authToken}`;
                    document.getElementById('tokenDisplay').classList.add('active');
                    showNotification('Login successful!', 'success');
                } else {
                    showNotification('Login failed', 'error');
                }
                
                displayResponse('login', response.status, data);
            } catch (error) {
                displayResponse('login', 0, { error: error.message });
                showNotification('Login request failed', 'error');
            }
        }

        async function testEndpoint(endpoint) {
            const config = endpoints[endpoint];
            
            if (config.requiresAuth && !authToken) {
                showNotification('Please login first', 'error');
                return;
            }

            const button = event.target;
            const originalText = button.textContent;
            button.innerHTML = '<div class="loading"><div class="spinner"></div>Sending...</div>';
            button.disabled = true;

            try {
                let url = config.url;
                let options = {
                    method: config.method,
                    headers: { 'Content-Type': 'application/json' }
                };

                if (config.requiresAuth) {
                    options.headers['Authorization'] = `Bearer ${authToken}`;
                }

                // Handle dynamic URLs
                if (endpoint === 'viewProject') {
                    url = document.getElementById('viewProjectUrl').value;
                } else if (endpoint === 'listTasks') {
                    url = document.getElementById('listTasksUrl').value;
                } else if (endpoint === 'viewTask') {
                    url = document.getElementById('viewTaskUrl').value;
                } else if (endpoint === 'deleteTask') {
                    url = document.getElementById('deleteTaskUrl').value;
                }

                // Handle request body
                const payloadElement = document.getElementById(`${endpoint}Payload`);
                if (payloadElement && (config.method === 'POST' || config.method === 'PUT')) {
                    options.body = payloadElement.value;
                } else if (payloadElement && config.method === 'GET' && endpoint === 'deleteProject') {
                    // Special case for delete project which uses GET with body
                    options.method = 'POST'; // Override to POST for body
                    options.body = payloadElement.value;
                }

                const response = await fetch(url, options);
                const data = await response.json();
                
                displayResponse(endpoint, response.status, data);
                showNotification(response.ok ? 'Request successful!' : 'Request failed', response.ok ? 'success' : 'error');

            } catch (error) {
                displayResponse(endpoint, 0, { error: error.message });
                showNotification('Request failed', 'error');
            } finally {
                button.textContent = originalText;
                button.disabled = false;
            }
        }

        function displayResponse(endpoint, status, data) {
            const responseSection = document.getElementById(`${endpoint}Response`);
            const statusBadge = document.getElementById(`${endpoint}Status`);
            const responseBody = document.getElementById(`${endpoint}Body`);

            responseSection.classList.remove('hidden');
            
            if (status >= 200 && status < 300) {
                statusBadge.textContent = `${status} Success`;
                statusBadge.className = 'status-badge status-success';
            } else {
                statusBadge.textContent = status === 0 ? 'Network Error' : `${status} Error`;
                statusBadge.className = 'status-badge status-error';
            }

            responseBody.textContent = JSON.stringify(data, null, 2);
        }

        function clearResponse(endpoint) {
            document.getElementById(`${endpoint}Response`).classList.add('hidden');
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => document.body.removeChild(notification), 300);
            }, 3000);
        }

        // Auto-format JSON
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.json-editor').forEach(editor => {
                editor.addEventListener('blur', function() {
                    try {
                        const parsed = JSON.parse(this.value);
                        this.value = JSON.stringify(parsed, null, 2);
                    } catch (e) {
                        // Invalid JSON, leave as is
                    }
                });
            });
        });
    </script>
</body>
</html>