<?php
class Player{

	// database connection and table name
	private $conn;
	private $table_name = "fb_players";

	// object properties
	public $id;
	public $first_name;
	public $last_name;
	public $position;
	public $nation;
	public $squad_id;
	public $squad_name;
	public $created;

	// constructor with $db as database connection
	public function __construct($db){
		$this->conn = $db;
	}

	// used to export records to csv
	public function export_CSV(){

		//select all data
		$query = "SELECT id, first_name, last_name, position, nation, created, modified FROM players";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		//this is how to get number of rows returned
		$num = $stmt->rowCount();

		$out = "ID,First Name,Last Name,Position,Nation,Created,Modified\n";

		if($num>0){
			//retrieve our table contents
			//fetch() is faster than fetchAll()
			//http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				//extract row
				//this will make $row['name'] to
				//just $name only
				extract($row);
				$out.="{$id},\"{$first_name}\",\"{$last_name}\",{$position},{$nation},{$created},{$modified}\n";
			}
		}

		return $out;
	}

	// used for paging players
	public function count(){
		$query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name . "";

		$stmt = $this->conn->prepare( $query );
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		return $row['total_rows'];
	}

	// used for paging players
	public function countSearch($keywords){

		$query = "SELECT COUNT(*) as total_rows
					FROM
						" . $this->table_name . " p
						LEFT JOIN fb_squads s
							ON p.squad_id = s.id
					WHERE p.last_name LIKE ? OR p.position LIKE ? OR s.name LIKE ?";

		$stmt = $this->conn->prepare($query);

		// sanitize
		$keywords=htmlspecialchars(strip_tags($keywords));
		$keywords = "%{$keywords}%";

		// bind variable values
		$stmt->bindParam(1, $keywords);
		$stmt->bindParam(2, $keywords);
		$stmt->bindParam(3, $keywords);

		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		return $row['total_rows'];
	}

	// create player
	function create(){

		// query to insert record
		$query = "INSERT INTO
					" . $this->table_name . "
				SET
					first_name=:first_name, last_name=:last_name, position=:position, nation=:nation, squad_id=:squad_id, created=:created";

		// prepare query
		$stmt = $this->conn->prepare($query);

		// sanitize
		$this->first_name=htmlspecialchars(strip_tags($this->first_name));
		$this->last_name=htmlspecialchars(strip_tags($this->last_name));
		$this->position=htmlspecialchars(strip_tags($this->position));
		$this->nation=htmlspecialchars(strip_tags($this->nation));
		$this->squad_id=htmlspecialchars(strip_tags($this->squad_id));
		$this->created=htmlspecialchars(strip_tags($this->created));

		// bind values
		$stmt->bindParam(":first_name", $this->first_name);
		$stmt->bindParam(":last_name", $this->last_name);
		$stmt->bindParam(":position", $this->position);
		$stmt->bindParam(":nation", $this->nation);
		$stmt->bindParam(":squad_id", $this->squad_id);
		$stmt->bindParam(":created", $this->created);

		// execute query
		if($stmt->execute()){
			return true;
		}else{
			echo "<pre>";
				print_r($stmt->errorInfo());
			echo "</pre>";

			return false;
		}
	}

	// read players
	public function read(){

		// select all query
		$query = "SELECT
					s.name as squad_name, p.id, p.last_name, p.position, p.nation, p.squad_id, p.created
				FROM
					" . $this->table_name . " p
					LEFT JOIN
						fb_squads s
							ON p.squad_id = s.id
				ORDER BY
					p.created DESC";

		// prepare query statement
		$stmt = $this->conn->prepare($query);

		// execute query
		$stmt->execute();

		return $stmt;
	}

	// search players with pagination
	function searchPaging($keywords, $from_record_num, $records_per_page){

		// select all query
		$query = "SELECT
					s.name as squad_name, p.id, p.last_name, p.position, p.nation, p.squad_id, p.created
				FROM
					" . $this->table_name . " p
					LEFT JOIN fb_squads s
						ON p.squad_id = s.id
				WHERE p.last_name LIKE ? OR p.position LIKE ? OR s.name LIKE ?
				ORDER BY p.created DESC
				LIMIT ?, ?";

		// prepare query statement
		$stmt = $this->conn->prepare($query);

		// sanitize
		$keywords=htmlspecialchars(strip_tags($keywords));
		$keywords = "%{$keywords}%";

		// bind variable values
		$stmt->bindParam(1, $keywords);
		$stmt->bindParam(2, $keywords);
		$stmt->bindParam(3, $keywords);
		$stmt->bindParam(4, $from_record_num, PDO::PARAM_INT);
		$stmt->bindParam(5, $records_per_page, PDO::PARAM_INT);

		// execute query
		$stmt->execute();

		return $stmt;
	}

	// search players
	function search($keywords){

		// select all query
		$query = "SELECT
					s.name as squad_name, p.id, p.last_name, p.position, p.nation, p.squad_id, p.created
				FROM
					" . $this->table_name . " p
					LEFT JOIN
						fb_squads c
							ON p.squad_id = s.id
				WHERE
					p.last_name LIKE ? OR p.position LIKE ? OR s.name LIKE ?
				ORDER BY
					p.created DESC";
		echo $query;
		// prepare query statement
		$stmt = $this->conn->prepare($query);

		// sanitize
		$keywords=htmlspecialchars(strip_tags($keywords));
		$keywords = "%{$keywords}%";

		// bind
		$stmt->bindParam(1, $keywords);
		$stmt->bindParam(2, $keywords);
		$stmt->bindParam(3, $keywords);

		// execute query
		$stmt->execute();

		return $stmt;
	}

	// read players
	function readAllPlayersByCategoryId(){

		// select all query
		$query = "SELECT
					s.name as squad_name, p.id, p.last_name, p.position, p.nation, p.squad_id, p.created
				FROM
					" . $this->table_name . " p
					LEFT JOIN
						fb_squads s
							ON p.squad_id = s.id
				WHERE
					p.squad_id = ?
				ORDER BY
					p.created DESC";

		// prepare query statement
		$stmt = $this->conn->prepare($query);

		// bind id of player to be updated
		$stmt->bindParam(1, $this->category_id);

		// execute query
		$stmt->execute();

		return $stmt;
	}

	// used when filling up the update player form
	function readOne(){

		// query to read single record
		$query = "SELECT
					s.name as squad_name, p.id, p.last_name, p.position, p.nation, p.squad_id, p.created
				FROM
					" . $this->table_name . " p
					LEFT JOIN
						fb_squads c
							ON p.squad_id = s.id
				WHERE
					p.id = ?
				LIMIT
					0,1";

		// prepare query statement
		$stmt = $this->conn->prepare( $query );

		// bind id of player to be updated
		$stmt->bindParam(1, $this->id);

		// execute query
		$stmt->execute();

		// get retrieved row
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		// set values to object properties
		$this->last_name = $row['last_name'];
		$this->nation = $row['nation'];
		$this->position = $row['position'];
		$this->squad_id = $row['squad_id'];
		$this->squad_name = $row['squad_name'];
	}

	// read players with pagination
	public function readPaging($from_record_num, $records_per_page){

		// select query
		$query = "SELECT
					s.name as squad_name, p.id, p.last_name, p.position, p.nation, p.squad_id, p.created
				FROM
					" . $this->table_name . " p
					LEFT JOIN
						fb_squads s
							ON p.squad_id = s.id
				ORDER BY p.created DESC
				LIMIT ?, ?";

		// prepare query statement
		$stmt = $this->conn->prepare( $query );

		// bind variable values
		$stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
		$stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);

		// execute query
		$stmt->execute();

		// return values from database
		return $stmt;
	}

	// update the player
	function update(){

		// update query
		$query = "UPDATE
					" . $this->table_name . "
				SET
					first_name = :first_name,
					last_name = :last_name,
					position = :position,
					nation = :nation,
					squad_id = :squad_id
				WHERE
					id = :id";

		// prepare query statement
		$stmt = $this->conn->prepare($query);

		// sanitize
		$this->first_name=htmlspecialchars(strip_tags($this->first_name));
		$this->last_name=htmlspecialchars(strip_tags($this->last_name));
		$this->position=htmlspecialchars(strip_tags($this->position));
		$this->nation=htmlspecialchars(strip_tags($this->nation));
		$this->squad_id=htmlspecialchars(strip_tags($this->squad_id));
		$this->id=htmlspecialchars(strip_tags($this->id));

		// bind new values
		$stmt->bindParam(':first_name', $this->first_name);
		$stmt->bindParam(':last_name', $this->last_name);
		$stmt->bindParam(':position', $this->position);
		$stmt->bindParam(':nation', $this->nation);
		$stmt->bindParam(':squad_id', $this->squad_id);
		$stmt->bindParam(':id', $this->id);

		// execute the query
		if($stmt->execute()){
			return true;
		}else{
			return false;
		}
	}

	// delete the player
	function delete(){

		// delete query
		$query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

		// prepare query
		$stmt = $this->conn->prepare($query);

		// sanitize
		$this->id=htmlspecialchars(strip_tags($this->id));

		// bind id of record to delete
		$stmt->bindParam(1, $this->id);

		// execute query
		if($stmt->execute()){
			return true;
		}

		return false;

	}

	// delete selected players
	public function deleteSelected($ids){

		$in_ids = str_repeat('?,', count($ids) - 1) . '?';

		// query to delete multiple records
		$query = "DELETE FROM " . $this->table_name . " WHERE id IN ({$in_ids})";

		$stmt = $this->conn->prepare($query);

		if($stmt->execute($ids)){
			return true;
		}else{
			return false;
		}
	}

}
?>
