//deleting items logic for incomes
//incomes to delete array
let deleteIncomesIdsArray = [];
//delete buttons for incomes
document.querySelectorAll(".deleteButtonIncome").forEach((button) => {
  button.addEventListener("click", (event) => {
    deleteIncomesIdsArray.push(
      parseInt(button.parentNode.getAttribute("data-id")),
    );
    event.target.parentNode.remove();
  });
});

//deleting items logic for expenses
//expenses to delete array
let deleteExpensesIdsArray = [];
//delete buttons for expenses
document.querySelectorAll(".deleteButtonExpense").forEach((button) => {
  button.addEventListener("click", (event) => {
    deleteExpensesIdsArray.push(
      parseInt(button.parentNode.getAttribute("data-id")),
    );
    event.target.parentNode.remove();
  });
});
//TODO: get the "save" button and attach an event listener which sends id's of deleted items to ../database/delete_items.php
function save () {
  //send deleted items to delete_items.php
  let $deleteData = {
    deleteIncomesIdsArray,
    deleteExpensesIdsArray,
  };
  fetch("../database/delete_items.php",
      {
        method: "POST",
        headers: {"Contents-type": "application/json"},
          body: JSON.stringify($deleteData),
      }
      ).then(response => response.text())
      .then(text => {
        try {
          const responseData = JSON.parse(text);
          console.log("Response data:", responseData);
          if (responseData.status === "success") {
          } else {
            alert("Failed to save items. Please try again.");
          }
        } catch (e) {
          console.error("Failed to parse response:", text);
          alert("Failed to save items. Please try again.");
        }
      })
      .catch(error => {
        console.error("Error:", error);
      });
  //TODO: get all the categories from all items and update the categories for items in the database
  const incomeItems = document.querySelectorAll(".income-item");
  const expenseItems = document.querySelectorAll(".expense-item");
  //get categories for update
  const incomesCategories = [];
  const expensesCategories = [];
  //get names for update
  const incomesNames = [];
  const expensesNames = [];
  //get values for updates
  const incomesAmounts = [];
  const expensesCosts = [];
  incomeItems.forEach(incomeItem => {
    const incomeId = incomeItem.querySelector('[data-id]');
    const incomeCategoryId = incomeItem.querySelector('[data-category-id]');
    incomesCategories.push({incomeId: incomeId, incomeCategoryId: incomeCategoryId});
    const incomeName = incomeItem.querySelector('.incomeName').value;
    incomesNames.push({incomeId: incomeId, incomeName: incomeName});
    const incomeAmount = incomeItem.getElementById("itemValue").value;
    incomesAmounts.push({incomeId: incomeId, incomeAmount: incomeAmount});
  });
  expenseItems.forEach(expenseItem => {
    const expenseId = expenseItem.querySelector('[data-id]');
    const expenseCategoryId = expenseItem.querySelector('[data-category-id]');
    expensesCategories.push({expenseId: expenseId, expenseCategoryId: expenseCategoryId});
    const expenseName = expenseItem.querySelector('.expenseName').value;
    expensesNames.push({expenseId: expenseId, expenseName: expenseName});
    const expenseCost = expenseItem.getElementById("itemValue").value;
    expensesCosts.push({expenseId: expenseId, expenseCost: expenseCost});
  });
  const requestData = {
    incomesCategories,
    expensesCategories,
    incomesNames,
    expensesNames,
    incomesAmounts,
    expensesCosts,
  };
  fetch("../database/update_items", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(requestData)
  }).then(response => response.text())  // Change to text to handle non-JSON responses
      .then(text => {
        try {
          const responseData = JSON.parse(text);
          console.log("Response data:", responseData);
          if (responseData.status === "success") {
            window.location.reload();
          } else {
            alert("Failed to save items. Please try again.");
          }
        } catch (e) {
          console.error("Failed to parse response:", text);
          alert("Failed to save items. Please try again.");
        }
      })
      .catch(error => {
        console.error("Error:", error);
      });
}


//TODO: selected with a checkbox incomes and expenses to a page like edit_budget but which sends the data to sendData.php