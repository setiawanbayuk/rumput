import sys
from pdfminer.pdfparser import PDFParser
from pdfminer.pdfdocument import PDFDocument
from pdfminer.pdfpage import PDFPage
from pdfminer.pdfpage import PDFTextExtractionNotAllowed
from pdfminer.pdfinterp import PDFResourceManager
from pdfminer.pdfinterp import PDFPageInterpreter
from pdfminer.pdfdevice import PDFDevice
from pdfminer.layout import LAParams
from pdfminer.converter import PDFPageAggregator
import pdfminer

myfile = sys.argv[1]

myflag = sys.argv[2]
# print(myfile)

# # Open a PDF file.
fp = open(myfile, 'rb')
# fp = open('combined.pdf', 'rb')

# Create a PDF parser object associated with the file object.
parser = PDFParser(fp)

# Create a PDF document object that stores the document structure.
# Password for initialization as 2nd parameter
document = PDFDocument(parser)

# Check if the document allows text extraction. If not, abort.
if not document.is_extractable:
    raise PDFTextExtractionNotAllowed

# Create a PDF resource manager object that stores shared resources.
rsrcmgr = PDFResourceManager()

# Create a PDF device object.
device = PDFDevice(rsrcmgr)

# BEGIN LAYOUT ANALYSIS
# Set parameters for analysis.
laparams = LAParams()

# Create a PDF page aggregator object.
device = PDFPageAggregator(rsrcmgr, laparams=laparams)

# Create a PDF interpreter object.
interpreter = PDFPageInterpreter(rsrcmgr, device)


def parse_obj(lt_objs, page):
    # loop over the object list
    # for obj in lt_objs:
    #     # if it's a textbox, print text and location
    #     if isinstance(obj, pdfminer.layout.LTTextBoxHorizontal):
    #         if obj.get_text().replace('\n', '') == myflag:
    #             print("tes")
    #             print("%s, %s, %s, %s, %s" % (
    #                 obj.bbox[0], obj.bbox[1], obj.bbox[2], obj.bbox[3], page))
    #             return True
    for obj in lt_objs:
        # if it's a textbox, print text and location
        if isinstance(obj, pdfminer.layout.LTTextBoxHorizontal):
            if myflag in obj.get_text():
                print("%s, %s, %s, %s, %s!" % (
                    obj.bbox[0], obj.bbox[1], obj.bbox[2], obj.bbox[3], page))
                #return True
# loop over all pages in the document
i = 0
for page in PDFPage.create_pages(document):

    # read the page into a layout object
    interpreter.process_page(page)
    layout = device.get_result()

    # extract text from this object
    if parse_obj(layout._objs, i):
        break
    i = i+1