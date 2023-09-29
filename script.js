function submit(user_id, idx) {
    let inputValue = document.getElementById('text').value;
    let priorityInputs = document.getElementsByName('priority');
    let priorityValue = null;

    for (let i = 0; i < priorityInputs.length; i++) {
        if (priorityInputs[i].checked) {
            priorityValue = priorityInputs[i].value;
            break;
        }
    }

    if (!inputValue.length || !priorityValue) {
        showToast("Error: сообщение не должно быть пустым, и приоритет должен быть выбран.");
        return;
    }

    fetch('http://todo.loc/add.php', {
        method: 'POST',
        headers: {
            "Content-type": "application/json"
        },
        body: JSON.stringify({
            'name': inputValue,
            'user_id': user_id,
            'priority': priorityValue
        })
    })
        .then(res => res.json())
        .then(response => {
            const newId = idx + 1;

            const name = response.name;
            const priority = response.priority;

            const newTodoItem = generateBaseTodoItem(newId, name, priority);

            document.getElementById('todoTableBody').appendChild(newTodoItem);
        })
        .catch(error => {
            console.log(error)
        })
}

function generateBaseTodoItem(id, description, priority) {
    let baseElement = document.createElement('tr');
    baseElement.className = 'todo-item';
    baseElement.id = 'row-' + id;
    baseElement.setAttribute('data-priority', priority);

    let th = document.createElement('th');
    th.scope = 'row';
    th.style.width = '10%';

    let itemIdSpan = document.createElement('span');
    itemIdSpan.className = 'item-id';
    itemIdSpan.textContent = id + '. ';

    let label = document.createElement('label');

    let checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.className = 'is_completed';
    checkbox.id = 'completed';
    checkbox.setAttribute('onclick', `checkElement(event, ${id})`);

    label.appendChild(checkbox);
    itemIdSpan.appendChild(label);
    th.appendChild(itemIdSpan);

    let descriptionCell = document.createElement('td');
    descriptionCell.className = 'item-name';
    descriptionCell.textContent = ' ' + description;

    let priorityCell = document.createElement('td');
    priorityCell.style.width = '15%';

    let priorityClass = '';
    switch (priority) {
        case 'low':
            priorityClass = 'text-success';
            break;
        case 'medium':
            priorityClass = 'text-warning';
            break;
        case 'high':
            priorityClass = 'text-danger fw-bold';
            break;
        default:
            priorityClass = '';
    }

    let priorityLabels = {
        'low': 'низкий',
        'medium': 'средний',
        'high': 'высокий'
    };

    // для каких целей выдается item-priority ${priorityClass}, если не используется нигде?
    priorityCell.className = `item-priority ${priorityClass}`;
    priorityCell.textContent = ' ' + priorityLabels[priority];

    let actionsCell = document.createElement('td');
    actionsCell.style.width = '15%';

    let actionButtonsDiv = document.createElement('div');
    actionButtonsDiv.className = 'action-buttons d-flex gap-2 justify-content-center';

    let deleteButton = document.createElement('button');
    deleteButton.textContent = 'Удалить';
    deleteButton.className = 'btn delete_btn btn-danger btn-sm mr-2';
    deleteButton.addEventListener('click', (event) => removeElement(event, id));

    let editButton = document.createElement('button');
    editButton.textContent = 'Редактировать';
    editButton.className = 'btn edit_btn btn-light btn-sm';
    editButton.setAttribute('data-bs-toggle', 'modal');
    editButton.setAttribute('data-bs-target', '#editTaskModal');
    editButton.setAttribute('data-task-name', description);
    editButton.addEventListener('click', (event) => editElement(id));

    actionButtonsDiv.appendChild(deleteButton);
    actionButtonsDiv.appendChild(editButton);

    actionsCell.appendChild(actionButtonsDiv);

    baseElement.appendChild(th);
    baseElement.appendChild(descriptionCell);
    baseElement.appendChild(priorityCell);
    baseElement.appendChild(actionsCell);

    return baseElement;
}

function removeElement(event, id) {
    fetch('http://todo.loc/delete.php', {
        method: 'DELETE',
        headers: {
            "Content-type": "application/json"
        },
        body: JSON.stringify({
            'id': id
        })
    }).then(res => res.json())
        .then(response => {
            showToast(response.message)
        })
        .catch(error => {
            console.error(error);
        });

    document.querySelector(`#row-${id}`).remove();
}

function saveChangesHandler(id) {
    const inputValue = document.getElementById('taskName').value;

    fetch('http://todo.loc/edit.php', {
        method: 'POST',
        headers: {
            "Content-type": "application/json"
        },
        body: JSON.stringify({
            'id': id,
            'name': inputValue
        })
    }).then(res => res.json())
        .then(response => {
            showToast(response.message)
            const todoRow = document.getElementById('row-' + id);
            const nameCell = todoRow.querySelector('.item-name');
            nameCell.textContent = response.name;

            document.getElementById('editTaskModal').classList.remove('show');
            document.body.classList.remove('modal-open');
        })
        .catch(error => {
            console.error(error);
        });
}

function editElement(id) {
    console.log('edit', id);

    document.getElementById('editTaskModal').classList.add('show');
    document.body.classList.add('modal-open');

    const taskNameInput = document.getElementById('taskName');
    taskNameInput.value = document.getElementById('row-' + id).querySelector('.item-name').textContent;

    const saveChangesBtn = document.getElementById('saveChangesBtn');
    const saveChangesHandlerWrapper = () => {
        saveChangesHandler(id);
        saveChangesBtn.removeEventListener('click', saveChangesHandlerWrapper);
    };
    saveChangesBtn.addEventListener('click', saveChangesHandlerWrapper);
}

function checkElement(event, id) {
    const
        checked = event.target.checked;

    fetch('http://todo.loc/check.php', {
        method: 'POST',
        headers: {
            "Content-type": "application/json"
        },
        body: JSON.stringify({
            'id': id,
            'checked': Number(checked)
        })
    }).then (res => res.json())
        .then (response => {
            showToast(response.message)
        })
}

function setFilterListeners() {
    const allFilter = document.getElementById("allFilter");
    const completedFilter = document.getElementById("completedFilter");
    const incompleteFilter = document.getElementById("incompleteFilter");
    const priorityFilter = document.getElementById("priority");

    function applyFilters() {
        const todoRows = document.querySelectorAll(".table tbody tr");
        const selectedPriority = priorityFilter.value;

        todoRows.forEach(row => {
            const isCompleted = row.querySelector(".is_completed").checked;
            const priority = row.getAttribute("data-priority");

            let shouldDisplay = true;

            if (
                (!allFilter.checked) &&
                ((!completedFilter.checked && isCompleted) || (!incompleteFilter.checked && !isCompleted)) ||
                (selectedPriority !== "all" && selectedPriority !== priority)
            ) {
                shouldDisplay = false;
            }
            row.style.display = shouldDisplay ? "table-row" : "none";
        });
    }
    allFilter.addEventListener("change", function () {
        if (allFilter.checked) {
            completedFilter.checked = false;
            incompleteFilter.checked = false;
        }
        applyFilters();
    });
    completedFilter.addEventListener("change", function () {
        if (completedFilter.checked) {
            allFilter.checked = false;
            incompleteFilter.checked = false;
        }
        applyFilters();
    });
    incompleteFilter.addEventListener("change", function () {
        if (incompleteFilter.checked) {
            allFilter.checked = false;
            completedFilter.checked = false;
        }
        applyFilters();
    });
    priorityFilter.addEventListener("change", applyFilters);

    applyFilters();
}

// initialize document.
setFilterListeners();

function initToast() {
    $('.toast').toast({
        autohide: true,
        delay: 5000
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initToast();
});

function showToast(message, type = 'success') {
    const toastElement = document.querySelector('.toast');
    const toastBody = toastElement.querySelector('.toast-body');

    toastBody.textContent = message;
    toastElement.classList.remove('bg-success');
    toastElement.classList.add(`bg-${type}`);

    $(toastElement).toast('show');
}