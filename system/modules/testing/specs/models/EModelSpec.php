<?php

class DescribeEModel extends TypolightContext
{
  public function itShouldInstantiate()
  {
    $model = new ExampleEModel1();

    $this->spec( $model )->should->beAnInstanceOf( 'EModel' );
    $this->spec( $model )->should->beAnInstanceOf( 'Model' );
    $this->spec( $model->id )->should->beEmpty();
  }


  public function itShouldInstantiateAndFind()
  {
    $model = new ExampleEModel1(1);

    $this->spec( $model->id )->should->be( '1' );
  }


  public function itShouldGetVirtualAttributeAndCacheResponse()
  {
    $model = new ExampleEModel1(1);

    $this->spec( $model->forMe )->should->be( 1 );
    $this->spec( $model->forMe )->should->be( 1 );
    $this->spec( $model->getForMe() )->should->be( 2 );
  }


  public function itShouldGetVirtualAttributeAndNotCacheResponseForUncachable()
  {
    $model = new ExampleEModel1(1);
    $time1 = $model->time;
    sleep(1);
    $time2 = $model->time;

    $this->spec( $time1 )->shouldNot->beEqualTo( $time2 );
  }


  public function itShouldSetVirtualAttributeAndCacheValue()
  {
    $model = new ExampleEModel1(1);
    $model->forMe = 5;

    $this->spec( $model->forMe )->should->match( '/5abc/' );
  }


  public function itShouldSetVirtualAttributeAndNotCacheValueForUncachable()
  {
    $model = new ExampleEModel1(1);
    $time1 = time();
    $model->time = $time1;
    sleep(1);
    $time2 = $model->time;

    $this->spec( $time1 )->shouldNot->beEqualTo( $time2 );
  }


  public function itShouldSetLanguageArrayForThisModel()
  {
    $model = new ExampleEModel1(1);
    $lang  = $model->language;

    $this->spec( $lang[ 'test_text' ] )->should->match( '/^test text$/' );
  }


  public function itShouldSaveTheRecord()
  {
    $model        = new ExampleEModel1(1);
    $model->name  = 'itShouldSaveTheRecord';

    $this->spec( $model->save() )->should->be( 1 );

    $db       = Database::getInstance();
    $record   = $db->execute( 'select * from tl_example_emodel_1 where id = 1' );
    $record->next();

    $this->spec( $record->name )->should->match( '/itShouldSaveTheRecord/' );
  }


  public function itShouldUpdateAttributes()
  {
    $model  = new ExampleEModel1(1);
    $attrs  = array( 'name' => 'itShouldUpdateAttributes' );

    $this->spec( $model->update_attributes( $attrs ) )->should->be( 1 );

    $db       = Database::getInstance();
    $record   = $db->execute( 'select * from tl_example_emodel_1 where id = 1' );
    $record->next();

    $this->spec( $record->name )->should->match( '/itShouldUpdateAttributes/' );
  }


  public function itShouldNotUpdateFilteredAttributes()
  {
    $model  = new ExampleEModel1(1);
    $attrs  = array( 'name' => 'itShouldUpdateAttributes', 'tstamp' => 123 );
    try
    {
      $model->update_attributes( $attrs );
    }

    catch ( Exception $e ){}

    $db       = Database::getInstance();
    $record   = $db->execute( 'select * from tl_example_emodel_1 where id = 1' );
    $record->next();

    $this->spec( $record->tstamp )->shouldNot->be( 123 );
  }


  public function itShouldCreate()
  {
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldCreate' );
    $id     = $model->create( $attrs );

    $this->spec( $id )->shouldNot->beFalse();

    $db       = Database::getInstance();
    $record   = $db->execute( "select * from tl_example_emodel_1 where id = $id" );
    $record->next();

    $this->spec( $record->name )->should->match( '/itShouldCreate/' );
  }


  public function itShouldNotCreateWithFilteredAttributes()
  {
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldNotCreateWithFilteredAttributes', 'tstamp' => 123 );

    try
    {
      $id = $model->create( $attrs );
    }

    catch ( Exception $e ){}

    $this->spec( $id )->should->beNull();

    $model  = new ExampleEModel1();

    $this->spec( $model->findBy( 'id', $id ) )->should->beFalse();
  }


  public function itShouldUpdateTimeStamp()
  {
    $model = new ExampleEModel1(1);
    $tstp1 = $model->tstamp;
    $model->save();
    $tstp2 = $model->tstamp;

    $this->spec( tstp1 )->shouldNot->beEqualTo( tstp2 );
  }


  public function itShouldSetCreatedAtFieldAndReturnIdWhenInsertingNewRecord()
  {
    $model        = new ExampleEModel1();
    $model->name  = 'itShouldSetCreatedAtFieldAndReturnIdWhenInsertingNewRecord';
    $id           = $model->save();

    $this->spec( $id )->should->be( 3 );
    $this->spec( $model->created_at )->shouldNot->be( 0 );
  }


  public function itShouldGetAllRecords()
  {
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetAllRecords' );

    $model->create( $attrs );
    $models = $model->all;

    $this->spec( count( $models ) )->should->be( 2 );
  }


  public function itShouldGetAllRecordsWithCondition()
  {
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetAllRecords' );
    $model->create( $attrs );
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetAllRecords2' );
    $model->create( $attrs );

    $models = $model->getAll( 'id', array( 'id > ?', 1 ) );
    $this->spec( count( $models ) )->should->be( 2 );
  }


  public function itShouldGetAllRecordsSorted()
  {
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetAllRecords' );
    $model->create( $attrs );
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetAllRecords2' );
    $model->create( $attrs );
    
    $models = $model->getAll( 'id desc' );
    $this->spec( $models[0]->id )->should->be( 8 );
    $this->spec( $models[1]->id )->should->be( 7 );
    $this->spec( $models[2]->id )->should->be( 1 );
  }


  public function itShouldGetAllRecordsLimited()
  {
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetAllRecords' );
    $model->create( $attrs );
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetAllRecords2' );
    $model->create( $attrs );
    
    $models = $model->getAll( 'id desc', null, 1 );
    $this->spec( count( $models ) )->should->be( 1 );
  }


  public function itShouldGetAllByDynamic()
  {
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetAllByDynamic' );
    $model->create( $attrs );
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetAllByDynamic' );
    $model->create( $attrs );

    $models_all_by = $model->find_all_by_name( 'itShouldGetAllByDynamic' );
    $this->spec( count( $models_all_by ) )->should->be( 2 );
  }


  public function itShouldGetAllByAndByDynamic()
  {
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetAllByAndByDynamic' );
    $model->create( $attrs );
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetAllByAndByDynamic' );
    $model->create( $attrs );

    $models_all_by_and = $model->find_all_by_name_and_id( 'itShouldGetAllByAndByDynamic', 14 );
    $this->spec( $models_all_by_and[ 0 ]->id  )->should->be( 14 );
  }


  public function itShouldGetAllByAndNotByDynamic()
  {
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetAllByAndNotByDynamic' );
    $model->create( $attrs );
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetAllByAndNotByDynamic' );
    $model->create( $attrs );
    
    $models_all_by_and_not  = $model->find_all_by_name_and_not_id( 'itShouldGetAllByAndNotByDynamic', 16 );
    $this->spec( count( $models_all_by_and_not ) )->should->be( 1 );
    $this->spec( $models_all_by_and_not[ 0 ]->id  )->should->be( 15 );
  }


  public function itShouldGetAllOrderByDynamic()
  {
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetAllOrderByDynamic' );
    $model->create( $attrs );
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetAllOrderByDynamic' );
    $model->create( $attrs );
    
    $models_all_by_order_by = $model->find_all_by_name_order_by_id_desc( 'itShouldGetAllOrderByDynamic' );
    $this->spec( count( $models_all_by_order_by ) )->should->be( 2 );
    $this->spec( $models_all_by_order_by[ 0 ]->id  )->should->be( 18 );
    $this->spec( $models_all_by_order_by[ 1 ]->id  )->should->be( 17 );
  }


  public function itShouldGetFirstByDynamic()
  {
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetFirstByDynamic' );
    $model->create( $attrs );
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetFirstByDynamic' );
    $model->create( $attrs );
    
    $success = $model->find_first_by_name( 'itShouldGetFirstByDynamic' );
    $this->spec( $success )->should->beTrue();
    $this->spec( $model->id )->should->be( 19 );
  }


  public function itShouldGetFirstByAndByDynamic()
  {
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetFirstByAndByDynamic' );
    $model->create( $attrs );
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetFirstByAndByDynamic' );
    $model->create( $attrs );

    $success = $model->find_first_by_name_and_id( 'itShouldGetFirstByAndByDynamic', 22 );
    $this->spec( $success )->should->beTrue();
    $this->spec( $model->id )->should->be( 22 );
  }


  public function itShouldGetFirstByAndNotByDynamic()
  {
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetFirstByAndNotByDynamic' );
    $model->create( $attrs );
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetFirstByAndNotByDynamic' );
    $model->create( $attrs );

    $success = $model->find_first_by_name_and_not_id( 'itShouldGetFirstByAndNotByDynamic', 23 );
    $this->spec( $success )->should->beTrue();
    $this->spec( $model->id )->should->be( 24 );
  }


  public function itShouldGetFirstByOrderByDynamic()
  {
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetFirstByOrderByDynamic' );
    $model->create( $attrs );
    $model  = new ExampleEModel1();
    $attrs  = array( 'name' => 'itShouldGetFirstByOrderByDynamic' );
    $model->create( $attrs );

    $success = $model->find_first_by_name_order_by_id_desc( 'itShouldGetFirstByOrderByDynamic' );
    $this->spec( $success )->should->beTrue();
    $this->spec( $model->id )->should->be( 26 );
  }


  public function itShouldDeleteRecord()
  {
    $model = new ExampleEModel1(1);
    $this->spec( $model->delete() )->should->beTrue();

    $model  = new ExampleEModel1();
    $this->spec( $model->findBy( 'id', 1 ) )->should->beFalse();
  }


  public function itShouldValidatePresenceOf()
  {
    $model = new ExampleEModelValidations();
    $model->save();
    $errors = $model->errorsOn( 'name' );

    $this->spec( $errors[0] )->should->match( '/name is required/' );
  }


  public function itShouldValidateUniquenessOf()
  {
    $model = new ExampleEModelValidations();
    $model->name = 'a test name';
    $model->save();
    $errors = $model->errorsOn( 'name' );

    $this->spec( $errors[0] )->should->match( '/"a test name" is already taken/' );
  }


  public function itShouldValidateFormatOf()
  {
    $model = new ExampleEModelValidations();
    $model->phone = 'a wrong phone number';
    $model->save();
    $errors = $model->errorsOn( 'phone' );

    $this->spec( $errors[0] )->should->match( '/phone is not formated as expected/' );
  }


  public function itShouldValidateNumericalityOf()
  {
    $model = new ExampleEModelValidations();
    $model->phone = 'a wrong phone number';
    $model->save();
    $errors = $model->errorsOn( 'phone' );

    $this->spec( $errors[1] )->should->match( '/phone should be numerical/' );
  }


  public function itShouldValidateMinLengthOf()
  {
    $model = new ExampleEModelValidations();
    $model->name = 'a';
    $model->save();
    $errors = $model->errorsOn( 'name' );

    $this->spec( $errors[0] )->should->match( '/name should be at least 2 letters long/' );
  }


  public function itShouldValidateMaxLengthOf()
  {
    $model = new ExampleEModelValidations();
    $model->name = 'aaaaaaaaaaaaaaa';
    $model->save();
    $errors = $model->errorsOn( 'name' );

    $this->spec( $errors[0] )->should->match( '/name should be at most 12 letters long/' );
  }


  public function itShouldValidateAssociation()
  {
    $model = new ExampleEModelValidations();
    $model->save();
    $errors = $model->errorsOn( 'ExampleEModel1' );

    $this->spec( $errors[0] )->should->match( '/ExampleEModelValidations should be associated with ExampleEModel1/' );
  }


  public function itShouldValidateWithCustomValidation()
  {
    $model = new ExampleEModelValidations();
    $model->name = 'bad name';
    $model->save();
    $errors = $model->errorsOn( 'name' );

    $this->spec( $errors[0] )->should->match( '/bad name iz bad name/' );
  }


  public function itShouldCheckIfTableHasField()
  {
    $model = new ExampleEModel1();
    $this->spec( $model->hasField( 'name' ) )->should->beTrue();
    $this->spec( $model->hasField( 'dontexist' ) )->should->beFalse();
  }


  public function itShouldFlushCache()
  {
    $model = new ExampleEModel1( 1 );
    $this->spec( $model->name )->should->match( '/example1-1/' );
    $this->spec( $model->forMe )->should->be( 1 );
    $this->spec( $model->forMe )->should->be( 1 );
    $model->flushCache();
    $this->spec( $model->forMe )->should->be( 2 );
    $this->spec( $model->name )->should->match( '/example1-1/' );
  }


  public function itShouldGetChildHasOne()
  {
    $model = new ExampleEModelAssoc1( 1 );
    $child = $model->ExampleEModelAssoc2();

    $this->spec( $child->id )->should->be( 2 );
  }


  public function itShouldGetChildrenHasMany()
  {
    $model    = new ExampleEModelAssoc1( 1 );
    $children = $model->ExampleEModelAssoc3();

    $this->spec( count( $children ) )->should->be( 2 );
    $this->spec( $children[0]->id )->should->be( 1 );
    $this->spec( $children[1]->id )->should->be( 2 );
  }


  public function itShouldGetChildrenThrough()
  {
    $model    = new ExampleEModelAssoc1( 1 );
    $children = $model->ExampleEModelAssoc4();

    $this->spec( count( $children ) )->should->be( 2 );
    $this->spec( $children[0]->id )->should->be( 1 );
    $this->spec( $children[1]->id )->should->be( 2 );
  }


  public function itShouldGetParentBelongsTo()
  {
    $model = new ExampleEModelAssoc2( 1 );
    $child = $model->ExampleEModelAssoc1();

    $this->spec( $child->id )->should->be( 2 );
  }

  public function itShouldGetRelatedManyToMany()
  {
    $model   = new ExampleEModelAssoc1( 1 );
    $related = $model->ExampleEModelAssoc5();

    $this->spec( count( $related ) )->should->be( 2 );
    $this->spec( $related[0]->id )->should->be( 1 );
    $this->spec( $related[1]->id )->should->be( 2 );
  }


  public function itShouldGetTreeFromRecord()
  {
    $model    = new ExampleEModelAssoc1( 1 );
    $children = $model->descendants;

    $this->spec( count( $children ) )->should->be( 5 );
  }


  public function itShouldTestPaternity()
  {
    $model1 = new ExampleEModelAssoc1( 1 );
    $model2 = new ExampleEModelAssoc1( 2 );

    $this->spec( $model1->isParentOf( $model2 ) )->should->beTrue();
    $this->spec( $model1->isParentOf( $model1 ) )->should->beFalse();
  }


  public function itShouldTestChildhood()
  {
    $model1 = new ExampleEModelAssoc1( 1 );
    $model2 = new ExampleEModelAssoc1( 2 );

    $this->spec( $model2->isChildOf( $model1 ) )->should->beTrue();
    $this->spec( $model2->isChildOf( $model2 ) )->should->beFalse();
  }
}


