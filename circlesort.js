


var arr = [
	{weight:135},
	{weight:2},
	{weight:2},
	{weight:1},
	{weight:2},
	{weight:3},
	{weight:800},
	{weight:354},
	{weight:234},
	{weight:1},
	{weight:2},
	{weight:3},
	{weight:12},
	{weight:54},
	{weight:1},
	{weight:2},
	{weight:3},
	{weight:635},
	{weight:283},
	{weight:43},
	{weight:63},
	{weight:2},
	{weight:1},
	{weight:2},
	{weight:3},
	{weight:1},
	{weight:54},
	{weight:2},
	{weight:9},
	{weight:294}
];

function firstLastReorder (arr) {
	arr = arr.slice(); //create copy
	var newArr = [];

	while( arr.length ) {

		newArr.push( arr.shift() );
		if ( arr.length )
			newArr.push( arr.pop() );

	}

	return newArr;

}


function circleReorder (arr) {
	console.log("input array length: " + arr.length);

	var totalWeight = 0; //arr.reduce(function(a, b) { return a + b; });
	arr.forEach(function(e){ totalWeight += e.weight; });

	var averageWeight = totalWeight / arr.length;

	var numOverAverage = 0;
	arr.forEach(function(e){ if(e.weight > averageWeight) numOverAverage += 1; });





	// slice w/o params copies array
	var descArr = arr.slice().sort(function(a,b){
		if (a.weight === b.weight) return 0;
		if (a.weight >   b.weight) return -1;
		else                       return 1;
	});
	// console.log("descArr length: " + descArr.length);

	var newArr = descArr.splice(0, numOverAverage);
	newArr = firstLastReorder( newArr );

	lowArr = firstLastReorder( descArr );


	var groupSize = Math.floor(descArr.length / numOverAverage);
	var pointer = newArr.length;
	var direction = -1;

	while ( lowArr.length ) {
		for(var i = 0; i < groupSize; i++) {
			if (lowArr.length)
				newArr.splice(pointer,0, lowArr.pop() );
		}
		pointer += direction;
		if (pointer < 1) {
			pointer = 1;
			direction = 1;
			groupSize = 1;
		}
	}




	// var numGroups = Math.floor( arr.length / 3 );
	// var groupSize = 3;

	// var maxGroupWeight = 3 * averageWeight;
	// var temp;
	// var firstWeight;
	// var firstIndex = 1;

	// var weirdCountThing = Math.floor( numOverAverage / 2 );
	// var newArr = [];

	// for ( var i = 0; i < arr.length; i++ ) {
	// while ( descArr.length ) {

	// 	// you need to draw this to understand...
	// 	if ( firstIndex < descArr.length ) {
	// 		firstWeight = descArr[ firstIndex ].weight;
	// 		newArr.push( descArr.splice( firstIndex, 1)[0] );
	// 		firstIndex++;
	// 	}
	// 	else {
	// 		// console.log(firstWeight + 'first');
	// 		firstWeight = descArr[ descArr.length - 1 ].weight;
	// 		newArr.push( descArr.pop() );		
	// 	}
	// 	// else {
	// 	// 	firstWeight = descArr[0].weight;
	// 	// 	newArr.push( descArr.shift() );
	// 	// }


	// 	if ( descArr.length > 1 ) {
	// 		var last = descArr.length - 1;
	// 		var pointer, testWeight;
	// 		for ( var i = last; i > 0; i-- ) { // don't test zero, since we look ahead 1
	// 			pointer = i;
	// 			testWeight = firstWeight + descArr[i].weight + descArr[i - 1].weight;
	// 			if ( testWeight > maxGroupWeight ) {
	// 				break;
	// 			}
	// 		}

	// 		if ( pointer === last ) {

	// 			for ( var x = 0; x < groupSize; x++)
	// 				newArr.push( descArr.pop() );
	// 			// newArr.push( descArr.slice(pointer, pointer-1) );
	// 		}
	// 		else {
	// 			temp = descArr.splice(pointer-1, groupSize-1);
	// 			temp.forEach(function(e){
	// 				newArr.push( e );
	// 			});

	// 		}

	// 	}
	// 	else {
	// 		newArr.push( descArr.pop() ); 
	// 	}

	// }


	// console.log(' - -  -- - - - - - break -- - - -- - --- - ');

	// console.log("output array length: " + newArr.length);
	// console.log(newArr);
	// console.log("descArr length: " + newArr.length);

	// console.log(maxGroupWeight);
	return newArr;
}


// circleReorder(arr);