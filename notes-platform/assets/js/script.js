document.addEventListener('DOMContentLoaded', function() {
    // Confirm before deleting
    const deleteButtons = document.querySelectorAll('.btn-danger');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });
    
    // Toggle content display for notes
    const noteContents = document.querySelectorAll('.note-content');
    noteContents.forEach(content => {
        if (content.textContent.trim().length > 200) {
            const shortContent = content.textContent.trim().substring(0, 200) + '...';
            const fullContent = content.textContent.trim();
            const toggleBtn = document.createElement('button');
            toggleBtn.textContent = 'Read More';
            toggleBtn.className = 'btn btn-sm';
            toggleBtn.style.marginTop = '10px';
            
            content.textContent = shortContent;
            content.appendChild(toggleBtn);
            
            toggleBtn.addEventListener('click', function() {
                if (toggleBtn.textContent === 'Read More') {
                    content.textContent = fullContent;
                    toggleBtn.textContent = 'Show Less';
                    content.appendChild(toggleBtn);
                } else {
                    content.textContent = shortContent;
                    toggleBtn.textContent = 'Read More';
                    content.appendChild(toggleBtn);
                }
            });
        }
    });
});