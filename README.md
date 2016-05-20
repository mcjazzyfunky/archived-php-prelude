
# php-prelude

A PHP library to make the daily programming much easier by providing a facade API for some important aspects of application development:

# Table of Contents
1. [Lazy sequencing](#lazy-sequences)
2. [Dynamic objects](#dynamic-objects)
3. [Database access](#database-access)
1. [Scanning directories](#scanning-directories)
2. [File input and output](#file-input-and-output)
3. [CSV exports](#csv-exports)

# Lazy sequences

Creating a sequence from an array
```php
Seq::from(['a', 'b', 'c', 'd'])
// Result: <'a', 'b', 'c', 'd'>
```

Creating a lazy range of numbers 
```php
Seq::range(1, 5);
// Result: <1, 2, 3, 4> (right value is excluded)
```

Sequences are really lazy: The enumeration will only be performed as far as really needed
```php
Seq::range(1, 1000000000)
	->take(10)
	->max()
// Result: 10 (only 10 values have been enumerated)
```
Building an array of first 10 fibonacci numbers
```php
Seq::iterate([1, 1], function ($a, $b) {
        return $a + $b;
    })
    ->take(10)
    ->toArray()
// Result: [0, 1, 1, 2, 3, 5, 8, 13, 21, 34]
```
Creating a lazy sequence based on a generator function
```php
Seq::from(function () {
	for ($i = 0; $i < 1000000000; +$i) {
		yield $i;
	}
});
```

Empty sequence
```php
Seq::nil()
// Result: Empty sequence
```

Single element sequence
```php
Seq::of(42)
// Result: <42>
```

Filtering sequences
```php
Seq::from([1, 2, 7, 9, 12, 24, 33, 45])
    ->filter(function ($n) {
	    return $n % 3 === 0;
    })
// Result: <9, 12, 33, 45>
```

Mapping sequences
```php
Seq::from([1, 2, 3, 4, 5])
    ->map(function ($n) {
	    return $n * $n;
    })
// Result: <1, 4, 9, 16, 25>
```

Limiting sequences
```php
Seq::range(1, 100)
    ->skip(5)
    ->take(10)
// Result: <6, 7, 8, 9, 10, 11, 12, 13, 14, 15>
```

Concatenating sequences
```php
$seq1 = Seq::of(42)
$seq2= Seq::from([43, 44, 45])
$seq3 = Seq::range(46, 50)

Seq::concat($seq1, $seq2)
// Result: <42, 43, 44, 45>

Seq::concatMany([$seq1, $seq2, $seq3])
// Result: <42, 43, 44, 45, 46, 47, 48, 49>
```

Flattening sequences
```php
$seq = Seq::from([Seq::from([1, 2, 3]), $Seq::from([4, 5, 6])])
$seq->flatten()
// Result: <1, 2, 3, 4, 5, 6>
```

Traversing sequences
```php
$seq = Seq::from([1, 2, 3, 4, 5]);

foreach ($seq as $n) {
	print $n;
}
// Prints out 12345

// Same as
$seq->each(function ($n) {
	print $n;
});
```

And many other sequence operations (see API documentation for details) .....

# Dynamic objects

Instead of handling records in associative arrays, it's possible to use dynamic objects where each property can be accessed using "->" arrow.
The advantage is that this is syntactically much nicer and a it will throw a RuntimeException in case that someone will try to read a property that does not exist.
The disadvantage is that dynamic objects use PHP's magic functions internally which is much slower than accessing values in an associative array.

```php
$user = new DynObject([
    'firstName' => 'John',
    'lastName' => 'Doe';
]);

$user->city = 'Seattle';
$user->country = 'USA';

print "$user->firstName $user->lastName, $user->city $user->country";
// Prints out: John Doe, Seattle USA
```

# Database access

Executing query:

```php
$database
    ->query('delete from user')
	->execute();
// Will clear table 'user'
```
Executing query with bindings

```php
$userId = 12345;

$database
    ->query('delete from user where id=?')
    ->bind($userId)
	->execute();
// Will delete the record of user 12345
```
```php
$database
    ->query('delete from user where city=:city and country=:country')
    ->bind(['city' => 'Seattle', 'country' => 'USA'])
	->execute();
// Will delete all users from Seattle
```

Inserting many records  with the same query (internally, prepared statements will be used)
```php
$users = [
    [1, 'John', 'Doe', 'Boston', 'USA'],
	[2, 'Jane', 'Whoever', 'Portland', 'USA']];

$database
    ->multiQuery('insert into user values (?, ?, ?, ?, ?)')
    ->bindMany($users) // also lazy sequences would be allowed here
    ->process();
// will insert two new user records to table 'user'
```

Fetch a single value
```php
$database
	->query('select count(*) from user where country=:0 and city=:1')
	->bind([$country, $city])
	->fetchSingle()
// Result: Number of matching records
```

Fetch an array of numeric arrays
```php
$database
    ->query('select id, firstName, lastName from user where country=?')
    ->bind($country)
    ->fetchRows()

// Result: [[111, 'John', 'Doe'], [222, 'Jane', 'Whoever'], ...]
```

Fetch an array of associative arrays
```php    
$databse
    ->query('select id, firstName, lastName from user where country=?)',
    ->bind($country)
    ->fetchRecs()
// Result:
// [['id' => 111, 'firstName' => 'John', 'lastName' => 'Doe'],
//  ['id' => 222, 'firstName' => 'Jane', 'lastName' => 'Whoever'], ...]
```

Fetch a lazy sequence of numeric arrays
```php
$database
    ->query('select * from user where country=:0 and city=:1')
    ->bind([$country, $city])
    ->fetchSeqOfRows()
// Result:
//    <[111, 'John', 'Doe'],
//     [222, 'Jane', 'Whoever'], ...>
```

Fetch a lazy sequence of associative arrays
```php        
$database
    ->query('select id, firstName, lastName from user where country=?')
    ->bind($country)
    ->fetchSeqOfRecs()
// Result:
//    <['id' => 111, 'firstName' => 'John', 'lastName' => 'Doe'],
//     ['id' => 222, 'firstName' => 'Jane', 'lastName' => 'Whoever'], ...>
```

Fetching a lazy sequence of dynamic objects

```php
$users = 
    $database
        ->query('select id, firstName, lastName from user where country=?',
        ->bind($country)
        ->limit(100)
        ->fetchSeqOfDynObjects();
    
foreach ($user as $user) {
    print $user->id . ': ' . $user->firstName . ' ' . $user->lastName . "\n";
}
// Prints out the first 100 users from the selected country
```

# Scanning directories

Scanning a directory for certain files or subdirectories (method 'scan' will return a lazy sequence)

```php
PathScanner::create()
	->recursive()
	->includeFiles(['*.php', '*.json'])
	->excludeFiles(['*tmp*', '*temp*'])
	->excludeLinks()
	->forceAbsolute() // list absolute paths
	->sort(FileComparators::byFileSize())
	->listPaths() // list paths as strings otherwise File objects would be returned
	->scan('.') // scan current directory
	->toArray();
// Result: An array of all PHP and JSON file paths as strings in the
// current directory (including files in the subdirectories),
// excluding temporary files and symbolic links,
// sorted by file size (ascending)
```

# File input and output

FIle operation without the need to handle file pointers aka. stream explicitly:
No need to open or close resources.
Each IO operation will throw an IOException on error, that means that it is not necessary to check the result of each IO operation for being false, like in the underlying original PHP API.

Reading a file line by line (lazily)
```php
$lines =
    FileReader::from('input.txt')
        ->readSeq();

foreach ($lines as $Line) {
    print $line . "\n";
}
// Reads and prints out the content of the input file line by line
```
Reading a file completely into a string
```php
$content =
    FileReader::from('input.txt')
        ->readFully();
// The whole content will be read and returned.
// Similar to function file_get_contents, but will throw
// an IOException on error.
```

Determine the number of "error" lines in a certain log file.
```php
$errorLineCount =
    FileReader::fromFilename('path/to/logs/app.log')
        ->readLines()
        ->filter(function ($line) {
            return stripos($line, 'error') !== false);
        })
        ->count();
```

Writing to files

```php
$lines =
    Seq::range(1, 100)
        ->map(function ($n) {
            return 'Line ' . $n;
        });

FileWriter::fromFile('output.txt')
    ->writeSeq($lines);
// Write 99 lines to the file:
// "Line 1", "Line2", "Line3" etc.
```
Appendng a concrete text to the file

```php
FileWriter::fromFile('output.txt')
    ->append()
    ->writeFully('This text will be appended to the existing file');
```

# CSV exports

Also for CSV exports a nice fluent API is provided

```php
// Please be aware that the following recordsets vary
// structurally
$recs = [
    ['LAST_NAME' => 'Iverson',
     'FIRST_NAME' => 'Allen',
     'CITY' => 'Hampton',
     'COUNTRY' => 'USA'],
     
    ['FIRST_NAME' => 'Dirk',
     'LAST_NAME' => 'Nowitzki',
     'CITY' => 'Wuerzburg',
     'COUNTRY' => 'Germany'],
    
    ['Michael "Air"', 'Jordan', 'New York',
     'USA', 'This field will not be exported']
];

$format =
    CSVFormat::create()
        ->columns(['FIRST_NAME', 'LAST_NAME', 'CITY', 'COUNTRY'])
        ->delimiter(';')
        ->quoteChar('"');

CSVExporter::create()
    ->format($format)
    ->mapper(function ($rec) {
        // Add some clones in Vienna - just because we can  ;-)
        $rec2 = $rec;
        $rec2['LAST_NAME'] = 'Doppelganger';
        $rec2['CITY'] = 'Vienna';
        $rec2['COUNTRY'] = 'Austria';
        
        return Seq::from([$rec, $rec2]);
    })
    ->sourceCharset('UTF-8')
    ->targetCharset('ISO-8859-1')
    ->export(
	    Seq::from($recs),
        FileWriter::fromFile('php://stdout'));
            
// Will print out the following CSV formatted records to stdout:

// FIRST_NAME;LAST_NAME;CITY;COUNTRY
// Allen;Iverson;Hampton;USA
// Allen;Doppelganger;Vienna;Austria
// Dirk;Nowitzki;Wuerzburg;Germany
// Dirk;Doppelganger;Vienna;Austria
// "Michael ""Air""";Jordan;"New York";USA
// "Michael ""Air""";Doppelganger;Vienna;Austria
```

