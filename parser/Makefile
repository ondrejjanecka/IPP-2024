run:
	python3.10 parse.py < tests/test.test

stats:
	python3.10 parse.py --stats=stats.out --jumps --fwjumps --backjumps --badjumps < tests/test.test

dstats:
	python3.10 parse.py --stats=stats.out --jumps --fwjumps --backjumps --badjumps --stats=stats2.out --jumps --fwjumps < tests/test.test

tstats:
	python3.10 parse.py --stats=stats.out --jumps --fwjumps --backjumps --badjumps --stats=stats2.out --labels --stats=stats3.out --loc < tests/test.test

estats:
	python3.10 parse.py --stats=stats.out < tests/test.test

errstats:
	python3.10 parse.py --stats=stats.out --jumps --fwjumps --backjumps --badjumps --stats=stats.out --jumps 

all:
	python3.10 parse.py < tests/test.test
	python3.10 parse.py --stats=stats.out --jumps --fwjumps --backjumps --badjumps --stats=stats2.out --jumps --fwjumps < tests/test.test
	python3.10 parse.py --stats=stats.out --jumps --fwjumps --backjumps --badjumps --stats=stats2.out --labels --stats=stats3.out --loc < tests/test.test
	python3.10 parse.py --stats=stats.out < tests/test.test

clean:
	rm -f stats.out stats2.out stats3.out