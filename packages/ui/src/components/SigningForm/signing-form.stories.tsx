import { useState } from 'react'
import { ComponentStory, ComponentMeta } from '@storybook/react'
import SigningForm from './index'

export default {
  title: 'Components/SigningForm',
  component: SigningForm,
} as ComponentMeta<typeof SigningForm>

export const SigningFormModal: ComponentStory<typeof SigningForm> = () => {
  const [isModalOpen, setIsModalOpen] = useState<boolean>(true)

  return (
    <SigningForm isOpen={isModalOpen} onClose={() => setIsModalOpen(false)} />
  )
}
