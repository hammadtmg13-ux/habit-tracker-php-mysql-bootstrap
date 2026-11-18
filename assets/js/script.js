// HabitTrack - client-side helpers

function confirmDelete() {
    return confirm('Are you sure you want to delete this habit? Its check-in history will be deleted too.');
}

// Auto-submit the category filter when it changes (progressive enhancement)
document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            // Keep the typed value while filtering by category
            searchInput.form.querySelector('select[name="category"]').value = 'all';
        });
    }
});
