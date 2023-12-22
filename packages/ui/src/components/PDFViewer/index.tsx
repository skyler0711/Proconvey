type PropTypes = {
  url: string
}

const PDFViewer = ({ url }: PropTypes) => {
  return <iframe
    src={url}
    title={url}
    className="w-full h-[967px]"
  />
}

export default PDFViewer
