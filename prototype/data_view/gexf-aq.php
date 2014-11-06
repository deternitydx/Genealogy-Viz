<?php
// This file produces the GEXF format for the graph of all marriages and
// people.  This format should in the future produce easy ways of defining
// dynamic graphs, as its current format supports dynamic definitions.  For the
// full syntax, see http://gexf.net/format/.

/***
 * Example Format
 *
 * <?xml version="1.0" encoding="UTF-8"?>
 * <gexf xmlns="http://www.gexf.net/1.2draft" version="1.2">
 *     <meta lastmodifieddate="2009-03-20">
 *         <creator>Gexf.net</creator>
 *         <description>A hello world! file</description>
 *     </meta>
 *     <graph mode="static" defaultedgetype="directed">
 *          <nodes>
 *               <node id="0" label="Hello" />
 *               <node id="1" label="Word" />
 *          </nodes>
 *          <edges>
 *               <edge id="0" source="0" target="1" />
 *          </edges>
 *     </graph>
 * </gexf>
 ***/

header("Content-Type: text/xml");
// Opening of the file
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<gexf xmlns=\"http://www.gexf.net/1.2draft\" version=\"1.2\">\n";
echo "<meta lastmodifieddate=\"" . date("Y-m-d") . "\">\n";
echo "\t<creator>Robbie Hott</creator>\n";
echo "\t<description>Nauvoo Graph</description>\n";
echo "</meta>\n";
echo "<graph mode=\"static\" defaultedgetype=\"directed\">\n";


// Get the content from the database
//
// Need an array of marriages (nodes), submarriages (in case man/woman married multiple times), and people (links)
// For each male person with a child of marriageid:
//   Get their marriages (as husband)
//   Add person as a link from birth marriage to married-to marriage (use submarriages)
// Combine submarriages with same husband/wife pair or same husband (latter is better)
// Actually, could probably just use husband ID as marriage ID in the array and gexf, but would need to look up husband ID for child of marriage ID for each person (easy join)
//
//  select p.*, pm.PersonID from Person p, Marriage m, PersonMarriage pm where p.Gender='Male' and p.ChildOfMarriageID=m.ID and m.ID=pm.MarriageID and pm.Role = 'Husband';
//
// For each result
//  add marriage if it doesn't exist (p.ID, person's name as marriage label)
//  add link from pm.PersonID to p.ID (child->marriageof)
// Select on wives (similar select statement)
//  for each wife, add them to the marriages they've married into

$nodes = array();
$edges = array();

$db = pg_connect("host=nauvoo.iath.virginia.edu dbname=nauvoo_data user=nauvoo password=p7qNpqygYU");

// Query for all the main gender
$result = pg_query($db, "SELECT p.\"ID\",n.\"First\",n.\"Middle\",n.\"Last\",p.\"BirthDate\",p.\"DeathDate\",
    p.\"Gender\", p.\"BirthPlaceID\", pm.\"PersonID\" as \"ChildOf\"
    FROM public.\"Person\" p, public.\"Name\" n, public.\"PersonMarriage\" pm, public.\"ChurchOrgMembership\" c
    WHERE p.\"ID\"=n.\"PersonID\" AND n.\"Type\"='authoritative' AND p.\"Gender\" = 'Male' 
        AND pm.\"MarriageID\" = p.\"BiologicalChildOfMarriage\" AND pm.\"Role\" = 'Husband'
        AND c.\"PersonID\" = p.\"ID\" AND c.\"ChurchOrgID\" = 1
    ORDER BY p.\"ID\" asc");
if (!$result) {
    exit;
}

// For each (main level) person
while ($person = pg_fetch_array($result)) {
    // if they don't have a to-marriage, then add one for their ID.
    $nodes[$person["ID"]] = array(
        "id" => $person["ID"],
        "label" => htmlspecialchars($person["First"] . " " . $person["Last"] . " Marriage"));

    // Add the person link from their marriage of birth to their marriage of adulthood
    $edge = array(
        "source" => $person["ChildOf"],
        "target" => $person["ID"],
        "label" => htmlspecialchars($person["First"] . " " . $person["Last"]));

    // check that this edge is not already accounted for (inefficient)
    $inarray = false;
    foreach ($edges as $e) {
        if ($edge["source"] == $e["source"] && $edge["target"] == $e["target"]) {
            $inarray = true;
            break;
        }
    }
    if (!$inarray)
        array_push($edges, $edge);
}

// Query for all the secondary gender
$result = pg_query($db, "SELECT DISTINCT p.\"ID\",n.\"First\",n.\"Middle\",n.\"Last\",p.\"BirthDate\",p.\"DeathDate\",
    p.\"Gender\", p.\"BirthPlaceID\", pm.\"PersonID\" as \"ChildOf\", m.\"SpouseID\"
    FROM public.\"Person\" p, public.\"Name\" n, public.\"PersonMarriage\" pm, public.\"ChurchOrgMembership\" c,
        (SELECT DISTINCT m1.\"PersonID\" as \"PersonID\", m2.\"PersonID\" as \"SpouseID\" 
            FROM public.\"PersonMarriage\" m1, public.\"PersonMarriage\" m2
            WHERE m1.\"MarriageID\" = m2.\"MarriageID\" AND m1.\"Role\" = 'Wife' AND m2.\"Role\" = 'Husband' GROUP BY m1.\"PersonID\", m2.\"PersonID\") m
    WHERE p.\"ID\"=n.\"PersonID\" AND n.\"Type\"='authoritative' AND p.\"Gender\" = 'Female' 
        AND pm.\"MarriageID\" = p.\"BiologicalChildOfMarriage\" AND pm.\"Role\" = 'Husband'
        AND m.\"PersonID\" = p.\"ID\" AND c.\"PersonID\" = p.\"ID\" AND c.\"ChurchOrgID\" = 1
    ORDER BY p.\"ID\" asc");
if (!$result) {
    exit;
}

// For each (secondary level) person
while ($person = pg_fetch_array($result)) {
    // Add the person link from their marriage of birth to their marriage of adulthood
    $edge = array(
        "source" => $person["ChildOf"],
        "target" => $person["SpouseID"],
        "label" => htmlspecialchars($person["First"] . " " . $person["Last"]));
    
    // check that this edge is not already accounted for (inefficient)
    $inarray = false;
    foreach ($edges as $e) {
        if ($edge["source"] == $e["source"] && $edge["target"] == $e["target"]) {
            $inarray = true;
            break;
        }
    }
    if (!$inarray)
        array_push($edges, $edge);
}


// Nodes
echo "<nodes>\n";
foreach ($nodes as $node) {
    echo "\t<node ";
    foreach ($node as $key => $val) echo "$key = \"$val\" ";
    echo "/>\n";
}
echo "</nodes>\n";

// Edges
echo "<edges>\n";
foreach ($edges as $i => $edge) {
    echo "\t<edge ";
    echo "id = \"$i\" ";
    foreach ($edge as $key => $val) echo "$key = \"$val\" ";
    echo "/>\n";
}
echo "</edges>\n";

// Closing of the file
echo "</graph>\n";
echo "</gexf>\n";


?>
