<?php
?>

<html>
<head>
<title>Geneology Visualizations</title>

<link rel="stylesheet" href="css/style.css">
</head>

<body>
<h1>Geneology Visualizations</h1>
<h4>Robbie Hott, <a href="http://www.cs.virginia.edu/~jh2jf/">www.cs.virginia.edu/~jh2jf</a></h4>

<h2>Visualization Prototypes</h2>
<ul>
	<li><a href="prototype_click_chord.html">Sankey Marriages, Chord Popup</a>: Displays a sankey-like diagram of marriages, where the
	marriage units are represented by circles in the diagram.  
	People are links between marriages.  On clicking a marriage unit, this will open up a popup frame with a chord diagram of the marriage.  
	This version uses sample data in the test/ directory.</li>
	<li><a href="prototype_embedded_chord.html">Sankey Marriages, Embedded Chords</a>: Displays a sankey-like diagram of marriages, where the
	marriages are represented by their actual chord diagrams (this is very browser and memory intensive, and requires extra load time).  
	People are links between marriages.  On clicking a marriage unit, this will open up a popup frame with a chord diagram of the marriage.  
	This version uses sample data in the test/ directory.</li>
	<li><a href="chord.html">Chord Diagram</a>: Displays a the chord diagram of a marriage.  Given a husband's id (by argument "id") from the
	real database, it shows that man's marriages and children in chord form.  Links are created from women to their children in the marriage.</li>
</ul>

<h2>Data Views</h2>
<p>We have some real data that we are considering.  The links below allow access to some parts of that data.</p>
<ul>
	<li><a href="data_view/people.php">People</a>: all people in the database.</li>
	<li><a href="data_view/marriages.php">Marriages</a>: all marriages in the database.</li>
	<li><a href="data_view/multimarriages.php">Men with Multiple Wives</a>: list of all men and their number of wives (throughout their lifetime).</li>
	<li><a href="data_view/plural_before_1846.php">Men with Multiple Wives before 1845</a>: list second and later wives of men before the completion of the temple, Dec 10 1845.</li>
	<li><a href="data_view/plural.php">Widower Remarriages before 1845</a>: list of men who married a second or later wife (after the death of their first), before the completion of the temple, Dec 10 1845.</li>
</ul>

</body>
</html>

