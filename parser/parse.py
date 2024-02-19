import sys
from sys import stdin
import re

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

types = ["int", "bool", "string", "nil", "label", "type", "var"]
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

def ConvertToXML(value):
    value = value.replace("&", "&amp;")
    value = value.replace("<", "&lt;")
    value = value.replace(">", "&gt;")
    value = value.replace("\"", "&quot;")
    value = value.replace("'", "&apos;")
    return value

def PrintHeader():
    print("<?xml version=\"1.0\" encoding=\"utf-8\"?>")
    print("<program language=\"IPPcode24\">")
    return

def IntCheck(value):
    if value.isdigit() == False:
        pattern = r'^-?(0x[\dA-Fa-f]+|0o[0-7]+|\d+)$'
        if re.match(pattern, value) == None:
            ErrPrint("Lexical or syntax error there")
            sys.exit(LEX_SYN_ERR)
    return

def EscSeqCheck(value):
    list = []
    for i in range(len(value)):
        if value[i] == "\\":
            list.append(i)

    pattern = r'\\(\d{3})'
    for i in range(len(list)):
        if re.match(pattern, value[list[i]:list[i]+4]) == None:
            ErrPrint("Lexical or syntax error there")
            sys.exit(LEX_SYN_ERR)
    return

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

    if type not in types:
        ErrPrint("Lexical or syntax error there")
        sys.exit(LEX_SYN_ERR)        

    if type == "int":
        IntCheck(value)

    if value.count("\\") != 0:
        EscSeqCheck(value)
    
    value = ConvertToXML(value)

    print(f"    <arg{number} type=\"{type}\">{value}</arg{number}>")
    wordCounter += 1
    return

def PrintReadArg(number):
    global wordCounter
    type = "type"
    value = words[wordCounter]
    if value not in types:
        ErrPrint("Lexical or syntax error there")
        sys.exit(LEX_SYN_ERR)
    
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


def varSymb():
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

    if words[wordCounter-1] == "WRITE":
        type = words[wordCounter].split("@")[0]
        if type not in types and type != "GF" and type != "LF" and type != "TF":
            ErrPrint("Lexical or syntax error")
            sys.exit(LEX_SYN_ERR)

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

    if words[wordCounter].count("@") == 0:
        ErrPrint("Lexical or syntax error")
        sys.exit(LEX_SYN_ERR)

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
    PrintReadArg(2)
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
    "MOVE": varSymb,
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
    "NOT": varSymb,
    "INT2CHAR": varSymb,
    "STRI2INT": varSymbSymb,
    "READ": varType,
    "WRITE": symb,
    "CONCAT": varSymbSymb,
    "STRLEN": varSymb,
    "GETCHAR": varSymbSymb,
    "SETCHAR": varSymbSymb,
    "TYPE": varSymb,
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

    if words[0] == ".IPPcode24":
        ErrPrint("Header error")
        sys.exit(LEX_SYN_ERR)

    wordCounter = 0
    words[0] = words[0].upper()
    for i in range(len(words)):
        if words[wordCounter] != " " or words[wordCounter] != "\n" or words[wordCounter] != "\t":
            switch.get(words[wordCounter], lambda: ErrPrint(f"Lexical or syntax error {words}"))()
        
        if len(words) == wordCounter:
            return
    return


ArgumentCheck()
PrintHeader()
for line in stdin:
    LineCheck(line)

if wordCounter == 0 and orderCounter == 0:
    ErrPrint("Lexical or syntax error")
    sys.exit(HEADER_ERR)

print("</program>")