<?php

/**
 * Generated by PHPUnit_SkeletonGenerator on 2017-01-25 at 19:33:46.
 */
require_once '/Applications/Noalyss/apps/noalyss/htdocs/include/constant.php';
require_once '../class_row_descriptor.php';
class row_descriptorTest extends PHPUnit_Framework_TestCase {

    /**
     * @var row_descriptor
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new row_descriptor;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @covers row_descriptor::check_consistency
     * @todo   Implement testCheck_consistency().
     */
    public function testCheck_consistency() {
        $row = ["2","I Frais d'établissement","20","<<\$C20>>","yes"];
        $expected=[
            'linestyle' => '2',
            'rubrique'  => "I Frais d'établissement",
            'code' => '20',
            'variable' => '<<$C20>>',
            'flatten' => true];
        
        $descriptor= new row_descriptor;
        $this->assertTrue($descriptor->check_consistency($row, 3));
        $this->assertEquals($expected,
               $descriptor->csv_row );
    }

    /**
     * @covers row_descriptor::code_ranges
     * @todo   Implement testCode_ranges().
     */
    public function testCode_ranges() {
        $descriptor= new row_descriptor;
        $expected = [['left'=> '20', 'right'=>'20']];
        $this->assertEquals($descriptor->code_ranges('20'),$expected);
        $expected1 = [['left'=> '20', 'right'=>'24']];
        $this->assertEquals($descriptor->code_ranges('20/24'),$expected1);
    }

    /**
     * @covers row_descriptor::linetype
     * @todo   Implement testLinetype().
     */
    public function testLinetype() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers row_descriptor::variable_name
     * @todo   Implement testVariable_name().
     */
    public function testVariable_name() {
        $descriptor= new row_descriptor;
        $result =$descriptor->variable_name('parent',"<<\$C20>>");
        $this->assertEquals($result,"C20");
    }

    /**
     * @covers row_descriptor::get_code_ranges
     * @todo   Implement testGet_code_ranges().
     */
    public function testGet_code_ranges() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers row_descriptor::get_bilan_row
     * @todo   Implement testGet_bilan_row().
     */
    public function testGet_bilan_row() {
        $row = ["2","I Frais d'établissement","20","<<\$C20>>","yes"];
        $expected=[
            'linestyle' => 2,
            'linetype'  => 'parent',
            'label'     => "I Frais d'établissement",
            'poste'     => '20',
            'variable'  => 'C20',
            'solde'     => 0.0];
        
        $descriptor= new row_descriptor;
        $this->assertTrue($descriptor->check_consistency($row, 3));
        $this->assertEquals($expected,
               $descriptor->get_bilan_row());

    }
    public function testRow_desriptor() {
        // Remove the following lines when you implement this test.
        $this->assertEquals(["2","I Frais d'établissement","20","<<\$C20>>","yes"],
               ["2","I Frais d'établissement","20","<<\$C20>>","yes"] );
    }

}