import sys
from sys import stdin

# Return codes
OK = 0
PARAMS_ERR = 10
INPUT_ERR = 11
OUTPUT_ERR = 12
HEADER_ERR = 21
OPCODE_ERR = 22
LEX_SYN_ERR = 23
INTERNAL_ERR = 99
# Return codes end


orderCounter = 0
words = []
wordCounter = 0


# flags


def ErrPrint(err):
    print("ERR: " + err, file=sys.stderr)

def ArgumentCheck():
    argC = len(sys.argv)

    if argC == 1:
        return
    elif argC == 2:
        if sys.argv[1] == "--help":
            print("Help")
            sys.exit(OK)
    
    ErrPrint("Wrong number of arguments or combination of arguments")
    sys.exit(PARAMS_ERR)

def PrintInstructions(opcode):
    global orderCounter
    global wordCounter
    print(f"  <instruction order=\"{orderCounter}\" opcode=\"{opcode}\">")
    orderCounter += 1
    wordCounter += 1
    return

def PrintArg(number):
    global wordCounter
    type = words[wordCounter].split("@")[0]
    if type == "GF" or type == "LF" or type == "TF":
        type = "var"
        value = words[wordCounter]
    elif words[wordCounter].count("@") != 0:
        value = words[wordCounter].split("@", 1)[1]
    else:
        type = "type"
        value = words[wordCounter]

    print(f"    <arg{number} type=\"{type}\">{value}</arg{number}>")
    wordCounter += 1
    return

def PrintLabelArg(number):
    global wordCounter
    # if words[wordCounter].count("@") != 1:
    #     ErrPrint("Lexical or syntax error there")
    #     sys.exit(LEX_SYN_ERR)
    type = "label"
    value = words[wordCounter]
    print(f"    <arg{number} type=\"{type}\">{value}</arg{number}>")
    wordCounter += 1
    return

def PrintEndInstruction():
    print("  </instruction>")
    return


def varSym():
    global words
    global wordCounter
    if len(words) != 3:
        ErrPrint("Lexical or syntax error")
        sys.exit(LEX_SYN_ERR)
    
    PrintInstructions(words[wordCounter])
    PrintArg(1)
    PrintArg(2)
    PrintEndInstruction()
    return

def noArgs():
    global words
    global wordCounter
    if len(words) != 1:
        ErrPrint("Lexical or syntax error")
        sys.exit(LEX_SYN_ERR)
    PrintInstructions(words[wordCounter])
    PrintEndInstruction()
    return

def var():
    global words
    global wordCounter
    if len(words) != 2:
        ErrPrint("Lexical or syntax error")
        sys.exit(LEX_SYN_ERR)
    
    PrintInstructions(words[wordCounter])
    PrintArg(1)
    PrintEndInstruction()
    return

def label():
    global words
    global wordCounter
    if len(words) != 2:
        ErrPrint("Lexical or syntax error")
        sys.exit(LEX_SYN_ERR)
    
    PrintInstructions(words[wordCounter])
    PrintLabelArg(1)
    PrintEndInstruction()
    return

def symb():
    global words
    global wordCounter
    if len(words) != 2:
        ErrPrint("Lexical or syntax error")
        sys.exit(LEX_SYN_ERR)
    
    PrintInstructions(words[wordCounter])
    PrintArg(1)
    PrintEndInstruction()
    return

def varSymbSymb():
    global words
    global wordCounter
    if len(words) != 4:
        ErrPrint("Lexical or syntax error")
        sys.exit(LEX_SYN_ERR)

    PrintInstructions(words[wordCounter])
    PrintArg(1)
    PrintArg(2)
    PrintArg(3)
    PrintEndInstruction()
    return

def varType():
    global words
    global wordCounter
    if len(words) != 3:
        ErrPrint("Lexical or syntax error")
        print(words)
        sys.exit(LEX_SYN_ERR)
    
    PrintInstructions(words[wordCounter])
    PrintArg(1)
    PrintArg(2)
    PrintEndInstruction()
    return

def labelSymbSymb():
    global words
    global wordCounter
    if len(words) != 4:
        ErrPrint("Lexical or syntax error")
        sys.exit(LEX_SYN_ERR)

    PrintInstructions(words[wordCounter])
    PrintLabelArg(1)
    PrintArg(2)
    PrintArg(3)
    PrintEndInstruction()
    return


switch = {
    "MOVE": varSym,
    "CREATEFRAME": noArgs,
    "PUSHFRAME": noArgs,
    "POPFRAME": noArgs,
    "DEFVAR": var,
    "CALL": label,
    "RETURN": noArgs,
    "PUSHS": symb,
    "POPS": var,
    "ADD": varSymbSymb,
    "SUB": varSymbSymb,
    "MUL": varSymbSymb,
    "IDIV": varSymbSymb,
    "LT": varSymbSymb,
    "GT": varSymbSymb,
    "EQ": varSymbSymb,
    "AND": varSymbSymb,
    "OR": varSymbSymb,
    "NOT": varSymbSymb,
    "INT2CHAR": varSym,
    "STRI2INT": varSymbSymb,
    "READ": varType,
    "WRITE": symb,
    "CONCAT": varSymbSymb,
    "STRLEN": varSym,
    "GETCHAR": varSymbSymb,
    "SETCHAR": varSymbSymb,
    "TYPE": varSym,
    "LABEL": label,
    "JUMP": label,
    "JUMPIFEQ": labelSymbSymb,
    "JUMPIFNEQ": labelSymbSymb,
    "EXIT": symb,
    "DPRINT": symb,
    "BREAK": noArgs
}

def LineCheck(line):
    global orderCounter
    global words
    global wordCounter
    line = line.split("#")[0]
    words = line.split()

    if len(words) == 0:
        return
    if orderCounter == 0:
        if words[wordCounter] == ".IPPcode24" and len(words) == 1:
            orderCounter += 1
            return
        else:
            ErrPrint("Header error")
            sys.exit(HEADER_ERR)
    
    wordCounter = 0
    words[0] = words[0].upper()
    for i in range(len(words)):
        if words[wordCounter] != " " or words[wordCounter] != "\n" or words[wordCounter] != "\t":
            switch.get(words[wordCounter], lambda: ErrPrint(f"Lexical or syntax error {words}"))()
        
        if len(words) == wordCounter:
            return
    return


ArgumentCheck()
print("<?xml version=\"1.0\" encoding=\"utf-8\"?>")
print("<program language=\"IPPcode24\">")
for line in stdin:
    LineCheck(line)

print("</program>")