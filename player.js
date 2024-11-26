// Function to toggle like/unlike song
function likeSong(songId, button) {
    // Determine whether it's a 'like' or 'unlike' based on the button text
    const action = button.textContent.trim() === 'Like' ? 'like' : 'unlike';
    
    fetch(like-song.php?song_id=${songId}&action=${action})
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Toggle the button text based on the action
                if (action === 'like') {
                    button.textContent = 'Unlike';  // Change button to 'Unlike' after liking
                } else {
                    button.textContent = 'Like';  // Change button to 'Like' after unliking
                }
                alert(data.message);  // Alert success message
            } else {
                alert(data.message);  // Alert failure message if something went wrong
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your request.');
        });
}