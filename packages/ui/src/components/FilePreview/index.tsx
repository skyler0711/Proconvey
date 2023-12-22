import { useRef } from 'react'
import PDFViewer from '../PDFViewer'
import Modal from '../Modals'
import Button from '../Button'

type PropTypes = {
  title?: string
  url: string,
  isOpen: boolean,
  onClose: () => void
}

type Viewer = {
  nextPage: () => void
  previousPage: () => void
}

const FilePreview = ({ title, url, isOpen = false, onClose }: PropTypes) => {
  const viewerRef = useRef<Viewer>(null)

  return (
    <Modal isOpen={isOpen} onClose={onClose}>
      <Modal.Title>{title}</Modal.Title>
      <Modal.Content>
        <PDFViewer ref={viewerRef} url={url} />
      </Modal.Content>
      <Modal.Footer>
        <Button size="small" onClick={() => viewerRef.current!.nextPage()}>
          Next
        </Button>
        <Button size="small" variant="secondary" onClick={() => viewerRef.current!.previousPage()}>
          Previous
        </Button>
      </Modal.Footer>
    </Modal>
  )
}

export default FilePreview
