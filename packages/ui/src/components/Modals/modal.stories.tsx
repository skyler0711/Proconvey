import { useState } from 'react'
import { ComponentStory, ComponentMeta } from '@storybook/react'
import Modal from './index'
import Button from '../Button'

export default {
  title: 'Components/Modals',
  component: Modal,
} as ComponentMeta<typeof Modal>

export const BasicModal: ComponentStory<typeof Modal> = () => {
  const [isModalOpen, setIsModalOpen] = useState<boolean>(true)

  return (
    <Modal isOpen={isModalOpen} onClose={() => setIsModalOpen(false)}>
      <Modal.Title>Modal Title</Modal.Title>
      <Modal.Content>
        This is example content
      </Modal.Content>
      <Modal.Footer>
        <Button size="small" variant="secondary">Cancel</Button>
        <Button size="small">Save Changes</Button>
      </Modal.Footer>
    </Modal>
  )
}
