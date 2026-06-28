import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', function () {
    const monthlyCanvas = document.getElementById('monthlyChart');
    if (monthlyCanvas) {
        const labels = JSON.parse(monthlyCanvas.dataset.labels || '[]');
        const income = JSON.parse(monthlyCanvas.dataset.income || '[]');
        const expense = JSON.parse(monthlyCanvas.dataset.expense || '[]');
        initMonthlyChart(monthlyCanvas, labels, income, expense);
    }

    const categoryCanvas = document.getElementById('categoryChart');
    if (categoryCanvas) {
        const labels = JSON.parse(categoryCanvas.dataset.labels || '[]');
        const data = JSON.parse(categoryCanvas.dataset.data || '[]');
        const colors = JSON.parse(categoryCanvas.dataset.colors || '[]');
        initCategoryChart(categoryCanvas, labels, data, colors);
    }
});

function initMonthlyChart(ctx, labels, income, expense) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Income',
                    data: income,
                    backgroundColor: 'rgba(34, 197, 94, 0.7)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 1,
                },
                {
                    label: 'Expense',
                    data: expense,
                    backgroundColor: 'rgba(239, 68, 68, 0.7)',
                    borderColor: 'rgb(239, 68, 68)',
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
            },
            scales: {
                y: { beginAtZero: true },
            },
        },
    });
}

function initCategoryChart(ctx, labels, data, colors) {
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [
                {
                    data: data,
                    backgroundColor: colors,
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' },
            },
        },
    });
}
