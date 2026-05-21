import sys
from pdfminer.pdfparser import PDFParser
from pdfminer.pdfdocument import PDFDocument
from pdfminer.pdfpage import PDFPage
from pdfminer.pdfpage import PDFTextExtractionNotAllowed
from pdfminer.pdfinterp import PDFResourceManager, PDFPageInterpreter
from pdfminer.layout import LAParams, LTTextBoxHorizontal, LTTextLine, LTChar, LTAnno, LTContainer
from pdfminer.converter import PDFPageAggregator

# Usage: python coordinate.py file.pdf marker
myfile = sys.argv[1]
myflag = sys.argv[2]


def iter_layout(obj):
    if isinstance(obj, LTContainer):
        for child in obj:
            yield child
            yield from iter_layout(child)


def line_text_and_chars(line):
    text = ''
    chars = []
    for child in line:
        if isinstance(child, LTChar):
            text += child.get_text()
            chars.append(child)
        elif isinstance(child, LTAnno):
            text += child.get_text()
        else:
            try:
                t = child.get_text()
                text += t
            except Exception:
                pass
    return text, chars


def print_flag_bbox_from_line(line, page_no):
    text, chars = line_text_and_chars(line)
    clean_text = text.replace('\n', '')
    idx = clean_text.find(myflag)
    if idx < 0:
        return False

    # Map only real LTChar positions. Most markers are plain ASCII, so this is stable.
    real_chars = chars
    if len(real_chars) < idx + len(myflag):
        # Fallback line bbox if mapping is not possible, but still much tighter than textbox bbox.
        x0, y0, x1, y1 = line.bbox
    else:
        selected = real_chars[idx:idx + len(myflag)]
        x0 = min(c.bbox[0] for c in selected)
        y0 = min(c.bbox[1] for c in selected)
        x1 = max(c.bbox[2] for c in selected)
        y1 = max(c.bbox[3] for c in selected)

    print(f"{x0}, {y0}, {x1}, {y1}, {page_no}!")
    return True


with open(myfile, 'rb') as fp:
    parser = PDFParser(fp)
    document = PDFDocument(parser)

    if not document.is_extractable:
        raise PDFTextExtractionNotAllowed

    rsrcmgr = PDFResourceManager()
    laparams = LAParams()
    device = PDFPageAggregator(rsrcmgr, laparams=laparams)
    interpreter = PDFPageInterpreter(rsrcmgr, device)

    for page_no, page in enumerate(PDFPage.create_pages(document)):
        interpreter.process_page(page)
        layout = device.get_result()

        found_on_page = False
        for obj in iter_layout(layout):
            if isinstance(obj, LTTextLine):
                if print_flag_bbox_from_line(obj, page_no):
                    found_on_page = True

        # Backward-compatible fallback: if no exact line-level match, search textbox bbox.
        # This should rarely run; marker should be a single small line in the Word template.
        if not found_on_page:
            for obj in layout:
                if isinstance(obj, LTTextBoxHorizontal) and myflag in obj.get_text():
                    x0, y0, x1, y1 = obj.bbox
                    print(f"{x0}, {y0}, {x1}, {y1}, {page_no}!")
