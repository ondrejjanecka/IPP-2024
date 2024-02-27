# Makefile for packaging files

# Variables
PARSER_DIR = parser
INTERPRET_DIR = interpret
ARCHIVE_PARSER_NAME = xjanec33.zip
ARCHIVE_INTERPRET_NAME = xjanec33.zip
PARSER_FILES = $(PARSER_DIR)/parse.py $(PARSER_DIR)/rozsireni
README_FILE = $(PARSER_DIR)/readme1.md

# Target to create the parser archive
parser: $(ARCHIVE_PARSER_NAME)

# Target to create the interpret archive
interpret: $(ARCHIVE_INTERPRET_NAME)

# Target to create the parser archive
$(ARCHIVE_PARSER_NAME): $(README_FILE) $(PARSER_FILES)
	zip -j $@ $(README_FILE) $(PARSER_FILES)

# Clean target to remove the archives
clean:
	rm -f $(ARCHIVE_PARSER_NAME) $(ARCHIVE_INTERPRET_NAME)