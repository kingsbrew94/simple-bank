<?php namespace FLY\Libs\CRUD;

class AppService {

    private CRUDRepository $repo;

	public function __construct(AppDAO $dao) 
	{
		$this->repo = $dao;
	}

	public function save() 
	{
		return $this->repo->save();
	}
	
	public function findAll()
	{
		return $this->repo->fetchAll();
	} 

	public function findById($id)
	{
		return $this->repo->fetchById($id);
	}

	public function update($data=null)
	{
		return $this->repo->update($data);
	}

	public function delete($data = null) 
	{
		return $this->repo->delete($data);
	}

	public function deleteById($id) 
	{
		return $this->repo->deleteById($id);
	}

	public function getRepo()
	{
		return $this->repo;
	}
}