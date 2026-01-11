pour le model  lors en creer des fonction :





public function getAll(){
    $sql="SELECT * FROM table_name ";
    $result=$this->db=query($sql);


}
public function create(){
    $stmt= $this->db=prepare("INSERT INTO table_name ()   VALUES (?,?,?)");
    $stmt->execute([],[])

}





