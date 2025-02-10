import sys
from reportlab.pdfgen import canvas
from PyPDF2 import PdfFileWriter, PdfFileReader
from PIL import Image

print(sys.argv)
myfile = sys.argv[1]
# Create the watermark from an image
c = canvas.Canvas(
    'C:/xampp/htdocs/esurat/application/libraries/local_sign/watermark.pdf')

# Draw the image at x, y. I positioned the x,y to be where i like here
# img = Image.open("ttd.png")

im = Image.open("C:/xampp/htdocs/esurat/application/libraries/local_sign/ttd.png")
width, height = im.size

# c.drawImage(img, x, y, 100, 100 * 'ttd.png'.height / 'ttd.png'.width)
c.drawImage("C:/xampp/htdocs/esurat/application/libraries/local_sign/ttd.png",
            float(sys.argv[4]), float(sys.argv[5]) - 50, 150, 150 * height / width)

c.save()

# Get the watermark file you just created
watermark = PdfFileReader(
    open("C:/xampp/htdocs/esurat/application/libraries/local_sign/watermark.pdf", "rb"))

# Get our files ready
data_output = PdfFileWriter()
input_file = PdfFileReader(open(myfile, "rb"))

# Number of pages in input document
page_count = input_file.getNumPages()

# Go through all the input file pages to add a watermark to them
for page_number in range(page_count):
    print("Watermarking page {} of {}".format(page_number, page_count))
    # merge the watermark with the page
    input_page = input_file.getPage(page_number)
    if page_number == int(sys.argv[3]):
        input_page.mergePage(watermark.getPage(0))
    # add page from input file to output document
    data_output.addPage(input_page)

# finally, write "output" to document-output.pdf
outputFile = sys.argv[2]
with open(outputFile, "wb") as outputStream:
    data_output.write(outputStream)
