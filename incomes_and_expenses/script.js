const incomeItems = document.querySelectorAll(".income-item");
const expenseItems = document.querySelectorAll(".expense-item");

// Deleting items logic for incomes
let deleteIncomesIdsArray = [];
document.querySelectorAll(".deleteButtonIncome").forEach((button) => {
  button.addEventListener("click", (event) => {
    deleteIncomesIdsArray.push(
        parseInt(button.parentNode.getAttribute("data-id"))
    );
    event.target.parentNode.remove();
  });
});

// Deleting items logic for expenses
let deleteExpensesIdsArray = [];
document.querySelectorAll(".deleteButtonExpense").forEach((button) => {
  button.addEventListener("click", (event) => {
    deleteExpensesIdsArray.push(
        parseInt(button.parentNode.getAttribute("data-id"))
    );
    event.target.parentNode.remove();
  });
});

function save() {
  // Send deleted items to delete_items.php
  let deleteData = {
    deleteIncomesIdsArray: deleteIncomesIdsArray,
    deleteExpensesIdsArray: deleteExpensesIdsArray,
  };

  fetch("../database/delete_items.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(deleteData),
  })
      .then((response) => response.json())
      .then((responseData) => {
        console.log("Response data:", responseData);
        if (responseData.status === "success") {
          console.log("Items deleted successfully");
        } else {
          console.log("Failed to delete items. Please try again.");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });

  // Prepare data for updating items

  const incomesCategories = [];
  const expensesCategories = [];
  const incomesNames = [];
  const expensesNames = [];
  const incomesAmounts = [];
  const expensesCosts = [];

  incomeItems.forEach((incomeItem) => {
    if (incomeItem) {
      const incomeId = incomeItem.getAttribute("data-id");
      const incomeCategoryId = incomeItem.querySelector("select") ? incomeItem.querySelector("select").value : null;
      if (incomeCategoryId !== null) {
        incomesCategories.push({ incomeId: parseInt(incomeId), incomeCategoryId: parseInt(incomeCategoryId) });
      }

      const incomeNameElement = incomeItem.querySelector(".incomeName");
      const incomeAmountElement = incomeItem.querySelector("#itemValue");
      if (incomeNameElement && incomeAmountElement) {
        const incomeName = incomeNameElement.value;
        const incomeAmount = incomeAmountElement.value;
        incomesNames.push({ incomeId: parseInt(incomeId), incomeName: incomeName });
        incomesAmounts.push({ incomeId: parseInt(incomeId), incomeAmount: parseInt(incomeAmount) });
      }
    }
  });

  expenseItems.forEach((expenseItem) => {
    if (expenseItem) {
      const expenseId = expenseItem.getAttribute("data-id");
      const expenseCategoryId = expenseItem.querySelector("select") ? expenseItem.querySelector("select").value : null;
      if (expenseCategoryId !== null) {
        expensesCategories.push({ expenseId: parseInt(expenseId), expenseCategoryId: parseInt(expenseCategoryId) });
      }

      const expenseNameElement = expenseItem.querySelector(".expenseName");
      const expenseCostElement = expenseItem.querySelector("#itemValue");
      if (expenseNameElement && expenseCostElement) {
        const expenseName = expenseNameElement.value;
        const expenseCost = expenseCostElement.value;
        expensesNames.push({ expenseId: parseInt(expenseId), expenseName: expenseName });
        expensesCosts.push({ expenseId: parseInt(expenseId), expenseCost: parseInt(expenseCost) });
      }
    }
  });

  const requestData = {
    incomesCategories,
    expensesCategories,
    incomesNames: incomesNames.map((item, index) => ({ ...item, incomeAmount: incomesAmounts[index].incomeAmount })),
    expensesNames: expensesNames.map((item, index) => ({ ...item, expenseCost: expensesCosts[index].expenseCost })),
  };

  fetch("../database/update_items.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(requestData),
  })
      .then((response) => response.json())
      .then((responseData) => {
        console.log("Response data:", responseData);
        if (responseData.status === "success") {
          window.location.reload();
        } else {
          alert("Failed to update items. Please try again.");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Failed to update items. Please try again.");
      });
}

document.getElementById("saveButton").addEventListener("click", save);

//arrays of checked income and expense items
function constructBudget() {
  let checkedIncomeItems = [];
  let checkedExpenseItems = [];

  //getting checked income items
  incomeItems.forEach((income) => {
    const checkbox = income.querySelector("#defaultCheck1");
    if (checkbox && checkbox.checked) {
      const income_id = income.getAttribute("data-id");
      const income_name = income.querySelector(".incomeName").value;
      const income_amount = income.querySelector("#itemValue").value;
      const category_name = income.querySelector("select").value;

      //getting category_id
      const incomeSelect = income.querySelector("select");
      const selectedOption = incomeSelect ? incomeSelect.options[incomeSelect.selectedIndex] : null;
      const category_id = selectedOption ? selectedOption.getAttribute("data-category-id") : null;


        checkedIncomeItems.push({
          income_id: income_id,
          income_name: income_name,
          income_amount: income_amount,
          category_name: category_name,
          category_id: category_id,
        });
      }

  });

  //getting checked expense items
  expenseItems.forEach((expense) => {
    const checkbox = expense.querySelector("#defaultCheck1");
    if (checkbox && checkbox.checked) {
      const expense_id = expense.getAttribute("data-id");
      const expense_name = expense.querySelector(".expenseName").value;
      const expense_cost = expense.querySelector("#itemValue").value;
      const category_name = expense.querySelector("select").value;

      //getting category_id
      const expenseSelect = expense.querySelector("select");
      const selectedOption = expenseSelect ? expenseSelect.options[expenseSelect.selectedIndex] : null;
      const category_id = selectedOption ? selectedOption.getAttribute("data-category-id") : null;


        checkedExpenseItems.push({
          expense_id: expense_id,
          expense_name: expense_name,
          expense_cost: expense_cost,
          category_name: category_name,
          category_id: category_id,
        });

    }
  });

  const data = {
    checkedIncomeItems: checkedIncomeItems,
    checkedExpenseItems: checkedExpenseItems,
  };
  console.log("Sending data:");
  console.log(JSON.stringify(data));
  //sending data to budget_constructor
  fetch("../budget_constructor/budget_constructor.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  })
      .then((response) => response.json())
      .then((responseData) => {
        console.log("Response data:", responseData);
        if (responseData.status === "success") {
          window.location.href = "../budget_constructor/budget_constructor.php";
        } else {
          console.error("Failed to send data. Please try again.");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });
}

document.getElementById("constructBudgetButton").addEventListener("click", constructBudget);
