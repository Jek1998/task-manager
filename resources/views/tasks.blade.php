<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container mt-5">

        {{-- Панель пользователя --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Task Manager</h1>
            <div>
                <span class="me-3">👤 {{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Выйти</button>
                </form>
            </div>
        </div>

        <!-- Task Creation Form -->
        <form id="task-form" class="mb-3 d-flex gap-2">
            <input type="text" id="task-title" class="form-control" placeholder="Enter task title" required>
            <button type="submit" class="btn btn-primary">Add Task</button>
        </form>

        <!-- Task Summary -->
        <p id="task-summary" class="mt-3 text-muted"></p>

        <!-- Кнопки фильтрации -->
        <div class="mb-3">
            <div class="btn-group" role="group" aria-label="Фильтр задач">
                <button type="button" class="btn btn-outline-primary filter-btn active" data-filter="all">Все</button>
                <button type="button" class="btn btn-outline-primary filter-btn" data-filter="active">Активные</button>
                <button type="button" class="btn btn-outline-primary filter-btn" data-filter="completed">Завершённые</button>
                <button type="button" class="btn btn-outline-danger ms-3" id="clear-completed">Удалить завершённые</button>
            </div>
        </div>

        <!-- Task List -->
        <ul id="task-list" class="list-group"></ul>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AJAX-логика -->
    <script>
        let currentFilter = 'all';
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                xhrFields: {
                    withCredentials: true
                }
            });

            function loadTasks() {
                $.get('/api/tasks', function (tasks) {
                    $('#task-list').empty();
                    let filteredTasks = tasks.filter(task => {
                        if (currentFilter === 'active') return !task.completed;
                        if (currentFilter === 'completed') return task.completed;
                        return true;
                    });

                    filteredTasks.forEach(task => {
                        $('#task-list').append(`
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2 flex-grow-1 task-body" data-id="${task.id}">
                                    <input type="checkbox" class="form-check-input toggle-task" data-id="${task.id}" ${task.completed ? 'checked' : ''}>
                                    <span class="task-title ${task.completed ? 'text-decoration-line-through text-muted' : ''}" data-id="${task.id}">${task.title}</span>
                                    <input class="form-control form-control-sm edit-input d-none flex-grow-1" value="${task.title}">
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-secondary edit-task" data-id="${task.id}">✏️</button>
                                    <button class="btn btn-sm btn-danger delete-task" data-id="${task.id}">🗑</button>
                                </div>
                            </li>
                        `);
                    });

                    const total = tasks.length;
                    const active = tasks.filter(t => !t.completed).length;
                    const completed = tasks.filter(t => t.completed).length;
                    $('#task-summary').text(`Всего: ${total} | Активные: ${active} | Завершённые: ${completed}`);
                });
            }

            loadTasks();

            $(document).on('click', '.edit-task', function () {
                const container = $(this).closest('.list-group-item').find('.task-body');
                container.find('.task-title').addClass('d-none');
                container.find('.edit-input').removeClass('d-none').focus();
            });

            $(document).on('blur', '.edit-input', saveEdit);
            $(document).on('keydown', '.edit-input', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    $(this).blur();
                }
            });

            function saveEdit() {
                const input = $(this);
                const taskId = input.closest('li').find('.task-title').data('id');
                const newTitle = input.val().trim();

                if (newTitle === '') return;

                $.ajax({
                    url: '/api/tasks/' + taskId,
                    method: 'PATCH',
                    data: { title: newTitle },
                    success: function () {
                        loadTasks();
                    }
                });
            }

            $('#task-form').submit(function (e) {
                e.preventDefault();
                const title = $('#task-title').val();
                $.post('/api/tasks', { title }, function () {
                    $('#task-title').val('');
                    loadTasks();
                });
            });

            $(document).on('click', '.delete-task', function () {
                const taskId = $(this).data('id');
                $.ajax({
                    url: '/api/tasks/' + taskId,
                    type: 'DELETE',
                    success: function () {
                        loadTasks();
                    }
                });
            });

            $(document).on('change', '.toggle-task', function () {
                const taskId = $(this).data('id');
                $.ajax({
                    url: `/api/tasks/${taskId}/toggle`,
                    type: 'PATCH',
                    success: function () {
                        loadTasks();
                    }
                });
            });

            $('#clear-completed').click(function () {
                $.ajax({
                    url: '/api/tasks/completed',
                    type: 'DELETE',
                    success: function () {
                        loadTasks();
                    }
                });
            });

            $('.filter-btn').click(function () {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                currentFilter = $(this).data('filter');
                loadTasks();
            });
        });
    </script>
</body>
</html>
