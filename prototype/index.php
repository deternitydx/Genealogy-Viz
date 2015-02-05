<html>
<head>
<title>Geneology Visualizations</title>

<link rel="stylesheet" href="css/style.css">
</head>

<body>
<h1>Geneology Visualizations</h1>
<h4>Robbie Hott, <a href="http://www.cs.virginia.edu/~jh2jf/">www.cs.virginia.edu/~jh2jf</a></h4>
<h4>Notes <a href="http://www.cs.virginia.edu/~jh2jf/notes/">www.cs.virginia.edu/~jh2jf/notes/</a></h4>
<h2>Visualization Prototypes</h2>
<ul>
	<li><a href="chord.html">Chord Diagram</a>: Displays a chord diagram of a marriage.  Given a husband's id (by argument "id") from the
    real database, it shows that man's marriages and children in chord form.  Links are created from women to their children in the marriage.
    <ul><li><a href="chord.html?id=615">Sample Male-Oriented Chord Diagram</a>: Chord diagram of Brigham Young and his wives and children</li>
    <li><a href="chord.html?id=1907">Sample Female-Oriented Chord Diagram</a>: Chord diagram of Zina Huntington and her husbands and children</li>
    </ul></li>
	<li><a href="chord.html?id=615&temporal=1">Chord Diagram over Time</a>: Displays a chord diagram of the marriage, allowing the user to choose a time within
	the marriage to view or use a slider to step through the marriage.  Given a husband's id (by argument "id") from the real database, it shows 
	that man's marriages and children in chord form.  It also allows for a "time" argument of the form "YYYY-MM-DD" as the date of the marriage
	status to show  Links are created from women to their children in the marriage
    <ul><li><a href="chord.html?id=615&temporal=1">Sample Male-Oriented Temporal Chord Diagram</a>: Chord diagram of Brigham Young and his wives and children</li>
    <li><a href="chord.html?id=1907&temporal=1">Sample Female-Oriented Temporal Chord Diagram</a>: Chord diagram of Zina Huntington and her husbands and children</li>
    </ul></li>
	<li><a href="multi_chord.html">Chord Diagram Comparison over Time</a>: Displays two chord diagrams (Brigham Young and Joseph Smith) with a time slider.</li>
	<li><a href="marriageflow.html">Marriage Flow Network, Chord Popup</a>: Displays a sankey-like diagram of marriages, where the
	marriage units are represented by circles in the diagram.  
    People are hyperedges between marriages, utilizing a square node to define the person in the network.  
    On clicking a marriage unit, this will open up a popup frame with a chord diagram of the marriage.
        <ul> 
            <li>Patriarchal Samples
                <ul>
                    <li><a href="marriageflow.html?id=615,616,51049">Brigham Young and relatives</a></li>
                    <li><a href="marriageflow.html?id=495,496,12625,12626,12627,12629">Joseph Smith and relatives</a></li>
                    <li><a href="marriageflow.html?id=615,616,51049,495,496,12625,12626,12627,12629">Brigham Young, Joseph Smith, and relatives</a></li>
                </ul>
            </li>
            <li>Matriarchal Samples
                <ul>
                    <li><a href="marriageflow.html?id=1907&wife=1&levels=2">Zina Huntington and 2 levels of separation</a></li>
                </ul>
            </li>
        </ul>
    </li>
	<li>Samples of chord diagrams, both static and temporal, as well as flow diagrams, can be accessed for all <a href="data_view/aq.php">AQ Members</a> in our database.</li>
</ul>

<h2>Data Views Of UVA's Database (derived from initial NCP data)</h2>
<p>We have expanded the initial set of data from BYU's <a href="http://nauvoo.byu.edu">Nauvoo Community Project</a>, to include a richer set of data on the polygamous marriages of the Annointed Quorum and individuals linked to those members.  The links below allow access to some parts of that data.</p>
<ul>
	<li><a href="data_view/aq.php">AQ Members</a>: List of Annointed Quorum members currently in our database.</li>
    <li><a href="data_view/people.php">All People</a>: Searchable list of all people currently in our database (using Authoritative Name).</li>
    <li><form action="data_view/search.php" method="get">Search People: Search for all people in the database by name.  Uses a fuzzy search mechanism.  Results may be clicked to find a list of children. <br/>Name <input type="text" name="q" width="25"/> <input type="Submit" value="Search"></form>
    </li>
	<li><a href="data_view/marriages.php">All Marriages</a>: Searchable list of all marriages currently in our database (using Authoritative First/Last Names of people and Official Name of places).</li>
	<li><a href="data_view/places.php">All Places</a>: Searchable list of all places currently in our database (using Official Name).</li>
	<li><a href="data_view/documentdb.php">Database Organization</a>: HTML version of the database organization document.  Use pandoc to create a PDF version.</li>
	<li><a href="data_view/compare_excel.php">Excel Sheet Comparison</a>: Compares the Excel spreadsheet from Joseph side-by-side with our current database.</li>
</ul>

<h2>REST API Service</h2>
<p>There is an api service available for accessing the data needed for visualizations.  It always creates a JSON response.</p>
<ul>
	<li><a href="api/marriages_by_man.php">Marriages by Man</a>: Takes a husband's id and returns a JSON object containing all his wives, their children, and relationships between wives and children.</li>
	<li><a href="api/marriages_by_woman.php">Marriages by Woman</a>: Takes a wife's id and returns a JSON object containing all her husbands, their children, and relationships between husbands and children.</li>
	<li><a href="api/marriages.php">Marriages</a>: Returns all marriages in the database as a JSON data object.</li>
	<li><a href="api/people.php">People</a>: Returns all people in the database as a JSON data object.</li>
</ul>


<hr style="width:100%;text-align:center;border:1px solid black;"/>

<h2>Visualization Prototypes from Initial NCP Data</h2>
Using the initial database from the Nauvoo Community Project, we created sample visualizations to start collecting our ideas.
<ul>
	<li><a href="vizbyu/prototype_click_chord.html">Sankey Marriages, Chord Popup</a>: Displays a sankey-like diagram of marriages, where the
	marriage units are represented by circles in the diagram.  
	People are links between marriages.  On clicking a marriage unit, this will open up a popup frame with a chord diagram of the marriage. </li>
	<li><a href="vizbyu/prototype_embedded_chord.html">Sankey Marriages, Embedded Chords</a>: Displays a sankey-like diagram of marriages, where the
	marriages are represented by their actual chord diagrams (this is very browser and memory intensive, and requires extra load time).  
	People are links between marriages.  On clicking a marriage unit, this will open up a popup frame with a chord diagram of the marriage.  </li>
	<li><a href="vizbyu/chord.html">Chord Diagram</a>: Displays a chord diagram of a marriage.  Given a husband's id (by argument "id") from the
	real database, it shows that man's marriages and children in chord form.  Links are created from women to their children in the marriage.</li>
	<li><a href="vizbyu/chord_time.html?id=626">Chord Diagram over Time</a>: Displays a chord diagram of the marriage, allowing the user to choose a time within
	the marriage to view or use a slider to step through the marriage.  Given a husband's id (by argument "id") from the real database, it shows 
	that man's marriages and children in chord form.  It also allows for a "time" argument of the form "YYYY-MM-DD" as the date of the marriage
	status to show  Links are created from women to their children in the marriage.</li>
</ul>

<h2>Data Views of Initial NCP Data</h2>
<p>To better understand the initial NCP database and allow our researchers to easily find individuals and marriages to update, we provide a few simple data tables from our copy of the initial database.  The links below allow access to some parts of that data.</p>
<ul>
	<li><a href="data_view/jill_db/people.php">People</a>: all people in the database.</li>
	<li><a href="data_view/jill_db/marriages.php">Marriages</a>: all marriages in the database.</li>
	<li><a href="data_view/jill_db/multimarriages.php">Men with Multiple Wives</a>: list of all men and their number of wives (throughout their lifetime).</li>
	<li><a href="data_view/jill_db/polyandry.php">Women with Multiple Husbands</a>: list of all women and their number of husbands (throughout their lifetime).</li>
	<li><a href="data_view/jill_db/plural_before_1846.php">Men with Multiple Wives before 1845</a>: list second and later wives of men before the completion of the temple, Dec 10 1845.</li>
	<li><a href="data_view/jill_db/plural.php">Widower Remarriages before 1845</a>: list of men who married a second or later wife (after the death of their first), before the completion of the temple, Dec 10 1845.</li>
	<li><a href="data_view/jill_db/orgs.php">Membership in Church Organizations</a>: all organization membership in the database.</li>
</ul>

</body>
</html>

