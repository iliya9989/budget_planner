document.addEventListener("DOMContentLoaded", () => {
    const incomeItemsContainer = document.getElementById("incomeItemsContainer");
    const expenseItemsContainer = document.getElementById("expenseItemsContainer");
    const unusedItemsContainer = document.getElementById("unusedItemsContainer");
    const addCategoryButton = document.getElementById("addCategoryButton");
    const saveCategoriesButton = document.getElementById("saveCategoriesButton");
    const newCategoryName = document.getElementById("newCategoryName");

    // Track items marked for deletion
    const itemsMarkedForDeletion = new Set();

    const addCategory = (name, container, color) => {
        const item = document.createElement("div");
        item.classList.add("row", `bg-${color}`, "border", "rounded-3", "p-3", "my-1", "shadow", "text-light", "justify-content-around");
        item.setAttribute("data-id", "new");

        const itemName = document.createElement("p");
        itemName.innerText = name;
        itemName.classList.add("col-auto", "my-auto", "bg-light", "border", "rounded-5", `text-${color}-emphasis`, "text-center", "text-break");

        const itemDeleteButton = document.createElement("button");
        itemDeleteButton.classList.add("deleteButton", "btn", "col-1");
        itemDeleteButton.style.justifySelf = "end";
        itemDeleteButton.style.fontWeight = "bold";
        itemDeleteButton.innerText = "X";
        itemDeleteButton.addEventListener("click", () => {
            item.remove();
            // If the item is not new, add it to the deletion set
            if (item.getAttribute("data-id") !== "new") {
                itemsMarkedForDeletion.add(item.getAttribute("data-id"));
            }
        });

        item.appendChild(itemName);
        item.appendChild(itemDeleteButton);
        container.appendChild(item);
    };

    const setupDeleteButtons = () => {
        document.querySelectorAll(".deleteButton").forEach(button => {
            button.addEventListener("click", (event) => {
                const item = event.target.closest(".row");
                const categoryId = item.getAttribute("data-id");
                if (categoryId !== "new") {
                    itemsMarkedForDeletion.add(categoryId);
                }
                item.remove();
            });
        });
    };

    addCategoryButton.addEventListener("click", (event) => {
        event.preventDefault();
        if (newCategoryName.value.trim() !== "") {
            console.log("Adding new category: " + newCategoryName.value.trim());
            addCategory(newCategoryName.value.trim(), unusedItemsContainer, "warning");
            newCategoryName.value = "";
        }
    });

    saveCategoriesButton.addEventListener("click", () => {
        const categoriesToSave = [];
        const categoriesToDelete = Array.from(itemsMarkedForDeletion);
        const newCategories = [];

        [incomeItemsContainer, expenseItemsContainer, unusedItemsContainer].forEach(container => {
            container.querySelectorAll(".row").forEach(item => {
                const categoryId = item.getAttribute("data-id");
                const categoryName = item.querySelector("p").innerText;

                if (categoryId === "new") {
                    console.log("New category detected: " + categoryName);
                    newCategories.push({ name: categoryName });
                } else if (!itemsMarkedForDeletion.has(categoryId)) {
                    categoriesToSave.push({ id: categoryId, name: categoryName });
                }
            });
        });

        const requestData = {
            categoriesToSave,
            categoriesToDelete,
            newCategories
        };

        console.log("Sending data:", requestData);

        fetch("../database/updateCategories.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(requestData)
        })
            .then(response => response.text())  // Change to text to handle non-JSON responses
            .then(text => {
                try {
                    const responseData = JSON.parse(text);
                    console.log("Response data:", responseData);
                    if (responseData.status === "success") {
                        window.location.reload();
                    } else {
                        alert("Failed to save categories. Please try again.");
                    }
                } catch (e) {
                    console.error("Failed to parse response:", text);
                    alert("Failed to save categories. Please try again.");
                }
            })
            .catch(error => {
                console.error("Error:", error);
            });
    });

    setupDeleteButtons();
});
