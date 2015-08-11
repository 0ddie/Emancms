void setup() {
  // put your setup code here, to run once:
  Serial.begin(9600);

  char arrayOfAttributes[100][5][33];
  int numberOfAttributes = 0;


  //Attribute 1
  //Group ID
  arrayOfAttributes[0][0]['0', 'x', '0', '2', '0', '1'];
  //Attribute ID
  arrayOfAttributes[0][1]['0', 'x', '0', '0', '0', '0'];
  //Attribute Number
  arrayOfAttributes[0][2]['0', 'x', '0', '1', '9'];
  //Attribute Default Value
  arrayOfAttributes[0][3]['0', 'x', '0'];
  //Attribute Timeout
  arrayOfAttributes[0][4]['1', '5'];
  numberOfAttributes++;

  //Attribute 2
  //Group ID
  arrayOfAttributes[1][0]['0', 'x', '0', '0', '0', '0'];
  //Attribute ID
  arrayOfAttributes[1][1]['0', 'x', '0', '0', '0', '2'];
  //Attribute Number
  arrayOfAttributes[1][2]['0', 'x', '0', '1', '9'];
  //Attribute Default Value
  arrayOfAttributes[1][3]['0', 'x', '0'];
  //Attribute Timeout
  arrayOfAttributes[1][4]['1', '5'];
  numberOfAttributes++;

  Serial.println(arrayOfAttributes[0][0]);
}

void loop() {
  // put your main code here, to run repeatedly:

}

