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

    $this->spec( $id )->should->be( 2 );
    $this->spec( $model->created_at )->shouldNot->be( 0 );
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
}
