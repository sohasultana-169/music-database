function showPlaylistOptions(song_id) {
    document.getElementById('playlistModal').style.display = 'block';
    window.selectedSongId = song_id;
}

function closeModal() {
    document.getElementById('playlistModal').style.display = 'none';
}

function addToPlaylist() {
    const playlistSelect = document.getElementById('playlistSelect');
    const newPlaylistName = document.getElementById('newPlaylistName').value;

    let playlistId = playlistSelect.value;
    if (playlistId === 'new' && newPlaylistName) {
        // Create a new playlist (AJAX to create the playlist in the backend)
        playlistId = createNewPlaylist(newPlaylistName);
    }

    const songId = window.selectedSongId;

    fetch('add_to_playlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ song_id: songId, playlist_id: playlistId })
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              alert('Song added to playlist');
              closeModal();
          } else {
              alert('Failed to add song to playlist');
          }
      });
}

function createNewPlaylist(name) {
    return 999; // Placeholder for the new playlist ID
}
