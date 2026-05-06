

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="../../CSS/w3.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/fonts.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Discover</title>
</head>
<style>
	
body {
    display: flex;
    flex-direction: column; /* Stack items vertically */
    justify-content: flex-start; /* Align the content to the top */
    align-items: center; /* Center horizontally */
    margin: 0;
    text-align: center;
    overflow-y: auto;
}
	
.container {
    width: 80%;
    max-width: 600px;
    padding: 20px;
    margin-top: 30px; /* Ensure container starts below the header */
}

input[type="text"] {
    background-color: transparent; /* Darker background for the input */
    color: whitesmoke; /* White text in the input */
    border: 4px solid darkblue;
    padding: 10px;
    width: 100%;
    margin-bottom: 20px;
    border-radius: 5px;
	font-size: 15px;
}

#searchResults {margin-top: 20px;}

.result {
    margin: 10px 0;
    padding: 10px;
    background-color: transparent;
    border-radius: 5px;
    font-size: 18px;
}

.result a {
    color: whitesmoke;
    text-decoration: none;
}

.result a:hover {
    text-decoration: underline;
}

.demo {
    transition: all 0.3s ease; /* Smooth transition for hover effect */
}

.demo:hover {
    opacity: 0.8; /* Slightly reduce opacity on hover */
}

	
</style>
	

		
<body>
	
	
	

<div class="container">
    <h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>
    <input type="text" id="searchBar" placeholder="Search..." onkeyup="searchItems()" autocomplete="off">   
    <div id="searchResults"></div>
</div>
	
	
<!-- Footer -->
<footer class="site-footer">
  <div class="footer-content">
    <p class="footer-main">
      © 2025 <?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?> | <a href="../Xtras/Guides.php">Visit Guides for documentation</a>
    </p>
    <p class="footer-specific">
    Powered by <a href="https://github.com/Belthazor86/Elysium.git" target="_blank" rel="noopener noreferrer">Elysium</a> 
    </p>
  </div>
</footer>	
	
	

<script>
    async function searchItems() {
        const query = document.getElementById('searchBar').value;
        if (query.length < 3) {
            document.getElementById('searchResults').innerHTML = '';
            return;
        }

        try {
            const response = await fetch(`https://archive.org/advancedsearch.php?q=${encodeURIComponent(query)}&fl[]=title,identifier&rows=10&output=json`);
            const data = await response.json();

            // Show search results
            displayResults(data.response.docs);
        } catch (error) {
            console.error('Error fetching data:', error);
            document.getElementById('searchResults').innerHTML = 'Error fetching data.';
        }
    }

    function displayResults(results) {
        const resultsContainer = document.getElementById('searchResults');
        resultsContainer.innerHTML = ''; // Clear previous results

        if (results.length === 0) {
            resultsContainer.innerHTML = '<p>No results found.</p>';
        } else {
            results.forEach(result => {
                const resultElement = document.createElement('div');
                resultElement.classList.add('result', 'demo', 'w3-opacity', 'w3-hover-opacity-off');
                resultElement.innerHTML = `<strong><a href="https://archive.org/details/${result.identifier}" target="_blank">${result.title}</a></strong><br>`;
                resultsContainer.appendChild(resultElement);
            });
        }
    }
</script>
	
	





</body>
</html>
