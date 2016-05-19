
# php-prelude

A PHP library to make the daily programming much easier by providing a facade API for some important aspects of application development:

- Lazy sequencing
- Database access
- CSV exporting
- File input/output
- Directory scanning
- etc.

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

Sequences are lazy: The enumeration will only performed as far as really needed
```php
Seq::range(1, 1000000000)
	->take(10)
	->max()
// Result: 10 (only 10 values have been enumerated)
```
Building array of first 10 fibonacci numbers
```php
Seq::iterate([1, 1], function ($a, $b) {
        return $a + $b;
    })
    ->take(10)
    ->toArray()
// Result: [0, 1, 1, 2, 3, 5, 8, 13, 21, 34]
```
Creating a sequence based on a generator function
```php
Seq::from(function () {
	for ($i = 0; $i < 1000000000; +$i) {
		yield $i;
	}
}
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
// Result: <5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15>
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
Seq::flatten($seq)
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

And many, many other sequence operations .....


# Scanning directories

Scanning a directory for certain files or subdirectories

```php
PathScanner::create()
	->recursive()
	->includeFiles(['*.php', '*.json'])
	->excludeFiles('*tmp*')
	->excludeLinks()
	->forceAbsolute() // list absolute paths
	->sort(FileComparators::byFileSize())
	->listPaths() // list paths as strings otherwise File objects would be returned
	->scan('.') // scan current directory
	->toArray();
// Result: An array of all PHP and JSON file paths as strings in the
// current directory (including files in the subdirectories),
// excluding symbolic links 
```

# File input and output

FIle operation without the need to handle file pointers aka. stream explicitly:
No need to open or close resources.

```php
$lines =
    Seq::range(1, 100)
        ->map(function ($n) {
            return 'Line ' . $n;
        });

FileWriter::fromFile('output.txt')
    ->writeSeq($lines);
// Will write 99 lines to the file:
// "Line 1", "Line2", "Line3" etc.
```
Appendng a concrete text to the file

```php
FileWriter::fromFile('output.txt')
    ->append()
    ->writeFull('This text will be appended to the existing file');
```
