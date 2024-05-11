<?php
class Squad {

	// database connection and table name
	private $conn;
	private $table_name = "fb_squads";

	// object properties
	public $id;
	public $name;
	public $min_played;
	public $wins;
	public $draws;
	public $losses;
	public $points;
	public $created;

	public function __construct($db){
		$this->conn = $db;
	}

	// used to export records to csv
	public function export_CSV(){

		//select all data
		$query = "SELECT id, name, min_played, points, created, modified FROM " . $this->table_name;
		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		//this is how to get number of rows returned
		$num = $stmt->rowCount();

		$out = "ID,Name,MinPlayed,Points,Created,Modified\n";

		if($num>0){
			//retrieve our table contents
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				//extract row
				//this will make $row['name'] to
				//just $name only
				extract($row);
				$out.="{$id},\"{$name}\",\"{$min_played}\",\"{$points}\",{$created},{$modified}\n";
			}
		}

		return $out;
	}

	// delete selected categories
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

	public function readOne(){
		// read the details of category to be edited
		// select single record query
		$query = "SELECT name, description
				FROM " . $this->table_name . "
				WHERE id = ?
				LIMIT 0,1";

		// prepare query statement
		$stmt = $this->conn->prepare( $query );

		// bind selected record id
		$stmt->bindParam(1, $this->id);

		// execute the query
		$stmt->execute();

		// get record details
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		// assign values to object properties
		$this->name = $row['name'];
		$this->description = $row['description'];
	}

	public function update(){

		// update the squad
		$query = "UPDATE " . $this->table_name . "
				SET name = :name, description = :description
				WHERE id = :id";

		// prepare query statement
		$stmt = $this->conn->prepare($query);

		// sanitize
		$this->name=htmlspecialchars(strip_tags($this->name));
		$this->description=htmlspecialchars(strip_tags($this->description));
		$this->id=htmlspecialchars(strip_tags($this->id));

		// bind values
		$stmt->bindParam(':name', $this->name);
		$stmt->bindParam(':description', $this->description);
		$stmt->bindParam(':id', $this->id);

		// execute the query
		if($stmt->execute()){
			return true;
		}

		return false;
	}

	public function delete(){
		// delete query
		$query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

		// prepare query statement
		$stmt = $this->conn->prepare($query);

		// sanitize
		$this->id=htmlspecialchars(strip_tags($this->id));

		// bind record id
		$stmt->bindParam(1, $this->id);

		// execute the query
		if($stmt->execute()){
			return true;
		}

		return false;
	}

	public function create(){
		// create the squad
		// insert query
		$query = "INSERT INTO fb_squads
				SET name = ?, created = ?";

		// prepare query statement
		$stmt = $this->conn->prepare($query);

		// sanitize
		$this->name=htmlspecialchars(strip_tags($this->name));
		$this->created=htmlspecialchars(strip_tags($this->created));

		// bind values
		$stmt->bindParam(1, $this->name);
		$stmt->bindParam(3, $this->created);

		// execute query
		if($stmt->execute()){
			return true;
		}

		return false;
	}

	// get search results with pagination
	public function searchPaging($search_term, $from_record_num, $records_per_page){

		// search squad based on search term
		// search query
		$query = "SELECT id, name
				FROM " . $this->table_name . "
				WHERE name LIKE ? 
				ORDER BY name ASC
				LIMIT ?, ?";

		// prepare query statement
		$stmt = $this->conn->prepare($query);

		// bind  variables
		$query_search_term = "%{$search_term}%";

		$stmt->bindParam(1, $query_search_term);
		$stmt->bindParam(2, $query_search_term);
		$stmt->bindParam(3, $from_record_num, PDO::PARAM_INT);
		$stmt->bindParam(4, $records_per_page, PDO::PARAM_INT);

		// execute query
		$stmt->execute();

		return $stmt;
	}

	// count all squads
	public function count(){
		// query to count all data
		$query = "SELECT COUNT(*) as total_rows FROM fb_squads";

		// prepare query statement
		$stmt = $this->conn->prepare($query);

		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$total_rows = $row['total_rows'];

		return $total_rows;
	}

	// count all squads with search term
	public function countSearch($keywords){

		// search query
		$query = "SELECT COUNT(*) as total_rows FROM fb_squads WHERE name LIKE ?";

		// prepare query statement
		$stmt = $this->conn->prepare($query);

		// bind search term
		$keywords = "%{$keywords}%";
		$stmt->bindParam(1, $keywords);
		$stmt->bindParam(2, $keywords);

		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$total_rows = $row['total_rows'];

		return $total_rows;
	}

	// read all with paging
	public function readPaging($from_record_num, $records_per_page){
		// read all squads from the database
		$query = "SELECT id, name, min_played
				FROM " . $this->table_name . "
				ORDER BY id DESC
				LIMIT ?, ?";

		// prepare query statement
		$stmt = $this->conn->prepare( $query );

		// bind values
		$stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
		$stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);

		// execute query
		$stmt->execute();

		return $stmt;
	}

	// used by select drop-down list
	public function read(){

		//select all data
		$query = "SELECT
					id, name, min_played
				FROM
					" . $this->table_name . "
				ORDER BY
					name";

		$stmt = $this->conn->prepare( $query );
		$stmt->execute();

		return $stmt;
	}

	// search without pagination
	public function searchAll_WithoutPagination($keywords){
		//select all data
		$query = "SELECT
					id, name, min_played
				FROM
					" . $this->table_name . "
				WHERE
					name LIKE ? 
				ORDER BY
					name";

		$stmt = $this->conn->prepare( $query );

		// sanitize
		$keywords=htmlspecialchars(strip_tags($keywords));
		$keywords = "%{$keywords}%";

		// bind
		$stmt->bindParam(1, $keywords);
		$stmt->bindParam(2, $keywords);

		$stmt->execute();

		return $stmt;
	}

	// used to read category name by its ID
	function readNameById(){

		$query = "SELECT name FROM " . $this->table_name . " WHERE id = ? limit 0,1";

		$stmt = $this->conn->prepare( $query );
		$stmt->bindParam(1, $this->id);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$this->name = $row['name'];
	}
}
?>
