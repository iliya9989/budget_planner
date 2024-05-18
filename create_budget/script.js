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
let totalIncome = 0;
let totalExpenses = 0;
let balance = 0;

// Submit event listener for income
incomeSubmitButton.addEventListener("click", (event) => {
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

  totalIncome += parseInt(itemValue.innerText);
  balance += parseInt(itemValue.innerText);
  totalIncomeField.innerText = `Total income: ${totalIncome}`;
  totalIncomeField.classList.remove("bg-secondary");
  totalIncomeField.classList.add("bg-success");
  incomeItemName.value = "";
  incomeItemValue.value = "";
  item.appendChild(itemName);
  item.appendChild(itemValue);
  item.appendChild(itemDeleteButton);
  incomeItemsContainer.appendChild(item);
  changeBalanceField();
});

// Submit event listener for expenses
expensesSubmitButton.addEventListener("click", (event) => {
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

  totalExpenses += parseInt(itemValue.innerText);
  balance -= parseInt(itemValue.innerText);
  totalExpensesField.innerText = `Total expenses: ${totalExpenses}`;
  totalExpensesField.classList.remove("bg-secondary");
  totalExpensesField.classList.add("bg-danger");
  expensesItemName.value = "";
  expensesItemValue.value = "";
  item.appendChild(itemName);
  item.appendChild(itemValue);
  item.appendChild(itemDeleteButton);
  expensesItemsContainer.appendChild(item);
  changeBalanceField();
});

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

// Function for sending budget data to index.html, which then sends them to the DB
function submitForms() {
  if (!incomeItemsContainer.hasChildNodes() || !expensesItemsContainer.hasChildNodes()) {
    alert("Please add at least one income item and one expense item before saving.");
    return;
  }

  let arrIncomeItems = {};
  let arrExpensesItems = {};
  const childDivsIncome = incomeItemsContainer.querySelectorAll("div");
  childDivsIncome.forEach((child, index) => {
    const name = child.getElementsByTagName("p")[0].textContent;
    const value = child.getElementsByTagName("p")[1].textContent;
    arrIncomeItems[name] = parseInt(value);
  });
  const childDivsExpenses = expensesItemsContainer.querySelectorAll("div");
  childDivsExpenses.forEach((child, index) => {
    const name = child.getElementsByTagName("p")[0].textContent;
    const value = child.getElementsByTagName("p")[1].textContent;
    arrExpensesItems[name] = parseInt(value);
  });
  const budgetID = document.getElementById("budget_id").innerText;
  const budgetName = document.getElementById("budgetName").value;
  const data = {
    incomeItems: arrIncomeItems,
    expensesItems: arrExpensesItems,
    budgetName: budgetName,
    budgetBalance: parseInt(balance),
    budget_id: parseInt(budgetID),
  };

  console.log('Sending data:', data); // Log data to the browser console

  fetch('../sendData.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
  })
      .then(response => response.json())
      .then(responseData => {
        console.log('Success:', responseData);
        window.location.href = '../index.php';
      })
      .catch((error) => {
        console.error('Error:', error);
      });
}

const saveTheBudgetButton = document.getElementById("saveTheBudgetButton");
saveTheBudgetButton.addEventListener("click", submitForms);
