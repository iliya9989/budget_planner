// Buttons
const incomeSubmitButton = document.getElementById("submitIncome");
const expensesSubmitButton = document.getElementById("submitExpenses");
// Income form
const incomeItemName = document.getElementById("incomeItemName");
const incomeItemValue = document.getElementById("incomeItemValue");
// Expenses form
const expensesItemName = document.getElementById("expensesItemName");
const expensesItemValue = document.getElementById("expensesItemValue");
// Containers for items
const incomeItemsContainer = document.getElementById("incomeItemsContainer");
const expensesItemsContainer = document.getElementById("expensesItemsContainer");
// "Total" fields
const totalIncomeField = document.getElementById("totalIncomeField");
const totalExpensesField = document.getElementById("totalExpensesField");
const balanceField = document.getElementById("balanceField");

// Counting variables
let balance = parseInt(document.getElementById("totalBalance").innerText);
let totalIncome = parseInt(document.getElementById("totalIncome").innerText);
let totalExpenses = parseInt(document.getElementById("totalExpenses").innerText);
totalIncomeField.innerText = `Total income: ${totalIncome}`;
totalIncomeField.classList.remove("bg-secondary");
totalIncomeField.classList.add("bg-success");
totalExpensesField.innerText = `Total expenses: ${totalExpenses}`;
totalExpensesField.classList.remove("bg-secondary");
totalExpensesField.classList.add("bg-danger");

const changeBalanceField = () => {
    balanceField.innerText = `Balance: ${balance}`;
    if (balance === 0 && incomeItemsContainer.hasChildNodes() && expensesItemsContainer.hasChildNodes()) {
        balanceField.classList.remove("bg-secondary");
        balanceField.classList.remove("bg-success");
        balanceField.classList.remove("bg-danger");
        balanceField.classList.add("bg-warning");
    } else if (balance > 0) {
        balanceField.classList.remove("bg-secondary");
        balanceField.classList.remove("bg-warning");
        balanceField.classList.remove("bg-danger");
        balanceField.classList.add("bg-success");
    } else if (balance < 0) {
        balanceField.classList.remove("bg-secondary");
        balanceField.classList.remove("bg-warning");
        balanceField.classList.remove("bg-success");
        balanceField.classList.add("bg-danger");
    } else {
        balanceField.classList.remove("bg-warning");
        balanceField.classList.remove("bg-success");
        balanceField.classList.remove("bg-danger");
        balanceField.classList.add("bg-secondary");
    }
};

changeBalanceField();

// Function to fetch categories and return as options
async function fetchCategories() {
    try {
        const response = await fetch("../database/get_categories.php");
        const categories = await response.json();
        return categories.map((category) => {
            const option = document.createElement("option");
            option.value = category.category_id;
            option.textContent = category.category_name;
            return option;
        });
    } catch (err) {
        console.error("Error fetching categories:", err);
        return [];
    }
}

// Function to validate category selection
function validateCategorySelection() {
    let valid = true;
    const childDivsIncome = incomeItemsContainer.querySelectorAll("div");
    childDivsIncome.forEach((child) => {
        const category_id = child.getElementsByTagName("select")[0].value;
        if (!category_id) {
            alert("Please select a category for all income items.");
            valid = false;
        }
    });

    const childDivsExpenses = expensesItemsContainer.querySelectorAll("div");
    childDivsExpenses.forEach((child) => {
        const category_id = child.getElementsByTagName("select")[0].value;
        if (!category_id) {
            alert("Please select a category for all expense items.");
            valid = false;
        }
    });

    return valid;
}

// Submit event listener for income
incomeSubmitButton.addEventListener("click", async (event) => {
    event.preventDefault();

    // Check if the values are valid
    if (!incomeItemName.value || !parseInt(incomeItemValue.value) || parseInt(incomeItemValue.value) <= 0) {
        incomeItemName.placeholder = "Please, enter valid values";
        throw new Error("Values are not valid");
    }

    // Creating an item
    const item = document.createElement("div");
    item.classList.add("row", "bg-success", "border", "rounded-3", "p-3", "my-1", "shadow", "text-light", "justify-content-around");

    // Adding content to the item
    const itemName = document.createElement("p");
    itemName.innerText = incomeItemName.value;
    itemName.classList.add("col-auto", "my-auto", "bg-light", "border", "rounded-5", "text-success-emphasis", "text-center", "text-break");

    const itemValue = document.createElement("p");
    itemValue.innerText = incomeItemValue.value;
    itemValue.classList.add("col-auto", "my-auto", "bg-light", "border", "rounded-5", "text-success-emphasis", "text-center", "text-break", "ms-1");

    const itemDeleteButton = document.createElement("button");
    itemDeleteButton.classList.add("btn", "col-1");
    itemDeleteButton.style.justifySelf = "end";
    itemDeleteButton.style.fontWeight = "bold";
    itemDeleteButton.innerText = "X";
    const removeItem = (event) => {
        totalIncome -= parseInt(itemValue.innerText);
        balance -= parseInt(itemValue.innerText);
        itemDeleteButton.removeEventListener("click", removeItem);
        event.target.parentElement.remove();
        totalIncomeField.innerText = `Total income: ${totalIncome}`;
        if (totalIncome === 0) {
            totalIncomeField.classList.remove("bg-success");
            totalIncomeField.classList.add("bg-secondary");
        }
        changeBalanceField();
    };
    itemDeleteButton.addEventListener("click", removeItem);

    // Select menu with categories for items
    const selectCategoryMenu = document.createElement("select");
    selectCategoryMenu.classList.add("form-select", "col-auto", "w-25");
    const defaultSelectOption = document.createElement("option");
    defaultSelectOption.innerText = "Category";
    defaultSelectOption.setAttribute("value", "");
    defaultSelectOption.setAttribute("disabled", "");
    defaultSelectOption.setAttribute("selected", "");
    selectCategoryMenu.appendChild(defaultSelectOption);

    const categoryOptions = await fetchCategories();
    categoryOptions.forEach(option => selectCategoryMenu.appendChild(option));

    totalIncome += parseInt(itemValue.innerText);
    balance += parseInt(itemValue.innerText);
    totalIncomeField.innerText = `Total income: ${totalIncome}`;
    totalIncomeField.classList.remove("bg-secondary");
    totalIncomeField.classList.add("bg-success");
    incomeItemName.value = "";
    incomeItemValue.value = "";
    item.appendChild(itemName);
    item.appendChild(itemValue);
    item.appendChild(selectCategoryMenu);
    item.appendChild(itemDeleteButton);
    incomeItemsContainer.appendChild(item);
    changeBalanceField();
});

// Submit event listener for expenses
expensesSubmitButton.addEventListener("click", async (event) => {
    event.preventDefault();

    // Check if the values are valid
    if (!expensesItemName.value || !parseInt(expensesItemValue.value) || parseInt(expensesItemValue.value) <= 0) {
        expensesItemName.placeholder = "Please, enter valid values";
        throw new Error("Values are not valid");
    }

    // Creating an item
    const item = document.createElement("div");
    item.classList.add("row", "bg-danger", "border", "rounded-3", "p-3", "my-1", "shadow", "text-light", "justify-content-around");

    // Adding content to the item
    const itemName = document.createElement("p");
    itemName.innerText = expensesItemName.value;
    itemName.classList.add("col-auto", "my-auto", "bg-light", "border", "rounded-5", "text-danger-emphasis", "text-center", "text-break");

    const itemValue = document.createElement("p");
    itemValue.innerText = expensesItemValue.value;
    itemValue.classList.add("col-auto", "my-auto", "bg-light", "border", "rounded-5", "text-danger-emphasis", "text-center", "text-break", "ms-1");

    const itemDeleteButton = document.createElement("button");
    itemDeleteButton.classList.add("btn", "col-1");
    itemDeleteButton.style.justifySelf = "end";
    itemDeleteButton.style.fontWeight = "bold";
    itemDeleteButton.innerText = "X";
    const removeItem = (event) => {
        totalExpenses -= parseInt(itemValue.innerText);
        balance += parseInt(itemValue.innerText);
        itemDeleteButton.removeEventListener("click", removeItem);
        totalExpensesField.innerText = `Total expenses: ${totalExpenses}`;
        event.target.parentElement.remove();
        if (totalExpenses === 0) {
            totalExpensesField.classList.remove("bg-danger");
            totalExpensesField.classList.add("bg-secondary");
        }
        changeBalanceField();
    };
    itemDeleteButton.addEventListener("click", removeItem);

    // Select menu with categories for items
    const selectCategoryMenu = document.createElement("select");
    selectCategoryMenu.classList.add("form-select", "col-auto", "w-25");
    const defaultSelectOption = document.createElement("option");
    defaultSelectOption.innerText = "Category";
    defaultSelectOption.setAttribute("value", "");
    defaultSelectOption.setAttribute("disabled", "");
    defaultSelectOption.setAttribute("selected", "");
    selectCategoryMenu.appendChild(defaultSelectOption);

    const categoryOptions = await fetchCategories();
    categoryOptions.forEach(option => selectCategoryMenu.appendChild(option));

    totalExpenses += parseInt(itemValue.innerText);
    balance -= parseInt(itemValue.innerText);
    totalExpensesField.innerText = `Total expenses: ${totalExpenses}`;
    totalExpensesField.classList.remove("bg-secondary");
    totalExpensesField.classList.add("bg-danger");
    expensesItemName.value = "";
    expensesItemValue.value = "";
    item.appendChild(itemName);
    item.appendChild(itemValue);
    item.appendChild(selectCategoryMenu);
    item.appendChild(itemDeleteButton);
    expensesItemsContainer.appendChild(item);
    changeBalanceField();
});

// Function for sending budget data to index.html, which then sends them to the DB
function submitForms() {
    if (!validateCategorySelection()) {
        return; // Prevent submission if validation fails
    }

    let arrIncomeItems = [];
    let arrExpensesItems = [];
    const childDivsIncome = incomeItemsContainer.querySelectorAll("div");
    childDivsIncome.forEach((child, index) => {
        const id = child.getAttribute("data-id");
        const name = child.getElementsByTagName("p")[0].textContent;
        const value = child.getElementsByTagName("p")[1].textContent;
        const category_id = child.getElementsByTagName("select")[0].value;
        arrIncomeItems.push({ id, name, value, category_id });
    });
    const childDivsExpenses = expensesItemsContainer.querySelectorAll("div");
    childDivsExpenses.forEach((child, index) => {
        const id = child.getAttribute("data-id");
        const name = child.getElementsByTagName("p")[0].textContent;
        const value = child.getElementsByTagName("p")[1].textContent;
        const category_id = child.getElementsByTagName("select")[0].value;
        arrExpensesItems.push({ id, name, value, category_id });
    });
    const budgetName = document.getElementById("budgetName").value;
    const data = {
        incomeItems: arrIncomeItems,
        expensesItems: arrExpensesItems,
        budgetName: budgetName,
        budgetBalance: parseInt(balance),
    };

    console.log("Sending data:", data); // Log data to the browser console

    fetch("../database/sendData.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
    })
        .then((response) => response.json())
        .then((responseData) => {
            console.log("Success:", responseData);
            window.location.href = "../index.php";
        })
        .catch((error) => {
            console.error("Error:", error);
        });
}

const saveTheBudgetButton = document.getElementById("saveTheBudgetButton");
saveTheBudgetButton.addEventListener("click", submitForms);

document.querySelectorAll(".deleteButtonIncome").forEach((button) => {
    const itemValue = button.parentNode.querySelector("#itemValue").innerText;
    const removeItem = (event) => {
        totalIncome -= parseInt(itemValue);
        balance -= parseInt(itemValue);
        document.getElementById("totalIncomeField").innerText =
            `Total income: ${totalIncome}`;
        event.target.parentElement.remove();
        button.removeEventListener("click", removeItem);
        if (totalIncome === 0) {
            totalIncomeField.classList.remove("bg-success");
            totalIncomeField.classList.add("bg-secondary");
        }
        changeBalanceField();
    };
    button.addEventListener("click", removeItem);
});

document.querySelectorAll(".deleteButtonExpenses").forEach((button) => {
    const itemValue = button.parentNode.querySelector("#itemValue").innerText;
    const removeItem = (event) => {
        totalExpenses -= parseInt(itemValue);
        balance += parseInt(itemValue);
        document.getElementById("totalExpensesField").innerText =
            `Total expenses: ${totalExpenses}`;
        event.target.parentElement.remove();
        button.removeEventListener("click", removeItem);
        if (totalExpenses === 0) {
            totalExpensesField.classList.remove("bg-danger");
            totalExpensesField.classList.add("bg-secondary");
        }
        changeBalanceField();
    };
    button.addEventListener("click", removeItem);
});

// Function for deleting the budget
function deleteBudget() {
    const budget_id = document.getElementById("budget_id").innerText.trim();

    if (!budget_id) {
        alert("Invalid budget ID.");
        return;
    }

    const data = { budget_id: budget_id };

    fetch("../database/delete_budget.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
    })
        .then((response) => response.json())
        .then((responseData) => {
            console.log("Delete Success:", responseData);
            if (responseData.status === 'success') {
                window.location.href = "../index.php";
            } else {
                alert("Failed to delete the budget. Please try again.");
            }
        })
        .catch((error) => {
            console.error("Delete Error:", error);
        });
}

const deleteTheBudgetButton = document.getElementById("deleteTheBudgetButton");
deleteTheBudgetButton.addEventListener("click", deleteBudget);