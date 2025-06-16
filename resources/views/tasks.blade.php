<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Manager</title>

    <!-- Bootstrap CSS (через CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery (через CDN) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Task Manager</h1>

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
        <ul id="task-list" class="list-group">
            <!-- Задачи будут добавляться сюда через jQuery -->
        </ul>



    </div>

    <!-- Bootstrap JS (необязателен, если не используешь dropdown/modal) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AJAX-логика -->
    <script>
        let currentFilter = 'all';
        $(document).ready(function () {
            // Загрузить все задачи
            function loadTasks() {
                $.get('/api/tasks', function (tasks) {
                    $('#task-list').empty(); // ← удаляем старые элементы
                    
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

            // Переключение в режим редактирования
            $(document).on('click', '.edit-task', function () {
                const container = $(this).closest('.list-group-item').find('.task-body');
                container.find('.task-title').addClass('d-none');
                container.find('.edit-input').removeClass('d-none').focus();
            });

            // Сохранение при потере фокуса или на Enter
            $(document).on('blur', '.edit-input', saveEdit);
            $(document).on('keydown', '.edit-input', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    $(this).blur();
                }
            });

            function saveEdit() {
                const input = $(this);
                const container = input.closest('.list-group-item');
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


            // Обработчик добавления задачи
            $('#task-form').submit(function (e) {
                e.preventDefault();
                const title = $('#task-title').val();

                $.post('/api/tasks', { title }, function () {
                    $('#task-title').val('');
                    loadTasks();
                });
            });

            // Обработчик удаления задачи
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
            // Обработчик переключения выполнено
            $(document).on('change', '.toggle-task', function () {
                const taskId = $(this).data('id');

                $.ajax({
                    url: `/api/tasks/${taskId}/toggle`,
                    type: 'PATCH',
                    success: function () {
                        loadTasks(); //Перезагрузка задач
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
