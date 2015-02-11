<html>
<head>
<title>Genealogy Visualizations</title>

<link rel="stylesheet" href="css/style.css">
</head>

<body>
<h1>Genealogy Visualizations</h1>
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
<p>We have collaborated with BYU's <a href="http://nauvoo.byu.edu">Nauvoo Community Project</a>, who have supplied an initial database. Additional research was performed to include a richer set of data on the polygamous marriages of the Annointed Quorum and individuals linked to those members.</p> 
</body>
</html>

