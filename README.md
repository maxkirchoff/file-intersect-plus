file-intersect-plus
===================

A simple, flat directory intersect tool to compare files by name and do a filtered copy from that targeted directory into a new directory.

I built this because I had a directory of hand selected wedding photos in low-res from my wife, and a directory of ALL our wedding photos in high-res.
Simple answer, build a scripts, get a directory of hand selected high-res photos.

# Uses

### Do a intersect on two flat directories and copy the filtered list to a new directory
`php do_work.php source=dir1 target=dir2 filtered_result=dir3`

* `source`
is the directory that has all the needles, the files we know about.

* `target`
is the directory that contains the haystack, the files we know about plus other stuff.

* `filtered_result`
is the directory that will contain copies of all the files from the target directory that match the names of the files in the source directory