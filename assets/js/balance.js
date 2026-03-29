function createExpensesChart() {
    const categories = [];
    const amounts = [];

    const expenseCategories = $("#expenses .transactions-category");

    expenseCategories.each(function () {
        const categoryName = $(this).find(".category-header [data-category]").text().trim();
        const categoryAmount = $(this).find(".category-header [data-amount]").data("amount");

        categories.push(categoryName);
        amounts.push(categoryAmount);
    });

    new Chart('expensesChart', {
        type: 'pie',
        data: {
            labels: categories,
            datasets: [
                {
                    data: amounts,
                    label: "Expenses by category",
                    hoverOffset: 8
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: "left",
                    labels: {
                        color: "white",
                        boxWidth: 15
                    }
                }
            }
        }
    });
}

function calculateBalance() {
    const incomeItems = Array.from(document.querySelectorAll('.incomes-list .transaction-item[data-amount]'));
    const expenseItems = Array.from(document.querySelectorAll('.expenses-list .transaction-item[data-amount]'));

    let totalIncome = 0;
    let totalExpense = 0;

    incomeItems.forEach(item => totalIncome += Number(item.dataset.amount));
    expenseItems.forEach(item => totalExpense += Number(item.dataset.amount));

    const balance = totalIncome - totalExpense;
    return balance;
}

function addBalanceInfo() {
    const totalBalance = calculateBalance();
    const balanceElement = $(".total-balance span[data-amount]");
    const messageElement = $(".total-balance p");

    balanceElement.text(totalBalance);
    balanceElement.attr("data-amount", totalBalance);

    if (totalBalance > 0) {
        messageElement.text("You are managing your finances very well.").removeClass("text-danger").addClass("text-success");
    } else if (totalBalance < 0) {
        messageElement.text("Be careful! You are getting into debt.").removeClass("text-success").addClass("text-danger");
    } else {
        messageElement.text("Your balance is zero.").removeClass("text-success text-danger");
    }
}

function sortTransactionsByDate(containerElement) {
    const items = Array.from(containerElement.querySelectorAll('.transaction-item'));

    items.sort((a, b) => {
        const dateA = new Date(a.dataset.date);
        const dateB = new Date(b.dataset.date);
        return dateA - dateB;
    });

    items.forEach(item => containerElement.appendChild(item));
}

function sortCategories(containerElement) {
    const categories = Array.from(containerElement.querySelectorAll('.transactions-category'));

    categories.sort((a, b) => {
        const nameA = a.querySelector('[data-category]').dataset.category.toLowerCase();
        const nameB = b.querySelector('[data-category]').dataset.category.toLowerCase();

        return nameA.localeCompare(nameB);
    });

    categories.forEach(category => containerElement.appendChild(category));
}

function sortAll() {
    const incomesContainer = document.querySelector('.incomes-list');
    sortCategories(incomesContainer);

    incomesContainer.querySelectorAll('.transactions-list').forEach(list => {
        sortTransactionsByDate(list);
    });

    const expensesContainer = document.querySelector('.expenses-list');
    sortCategories(expensesContainer);

    expensesContainer.querySelectorAll('.transactions-list').forEach(list => {
        sortTransactionsByDate(list);
    });
}