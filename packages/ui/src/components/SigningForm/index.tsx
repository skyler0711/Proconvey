import Button from '../Button'
import Modal from '../Modals'

type PropTypes = {
  children?: React.ReactNode
  isOpen: boolean,
  onClose: () => void
}


const SigningForm = ({ isOpen = false, onClose }: PropTypes) => {
  return (
    <Modal isOpen={isOpen} onClose={onClose}>
      <Modal.Title>Sign the form</Modal.Title>
      <Modal.Content>
        <div className="rounded-lg flex items-center justify-center bg-blue-chalk h-[21.5625rem] mb-5">
          <p className="text-primary text-opacity-50 leading-[1.5625rem]">Draw your signature here</p>
        </div>
        <p className="text-lg leading-[1.5625rem] mb-5">I agree for my signature above to be valid means of signing</p>
      </Modal.Content>
      <Modal.Footer>
        <Button size="small">Sign</Button>
        <Button size="small" variant="secondary">Cancel</Button>
        <Button size="small" variant="secondary">Clear</Button>
      </Modal.Footer>
    </Modal>
  )
}

export default SigningForm
