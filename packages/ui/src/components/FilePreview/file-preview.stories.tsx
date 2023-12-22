import { useState } from 'react'
import { ComponentStory, ComponentMeta } from '@storybook/react'
import FilePreview from './index'

export default {
  title: 'Components/FilePreview',
  component: FilePreview,
} as ComponentMeta<typeof FilePreview>

export const FilePreviewModal: ComponentStory<typeof FilePreview> = () => {
  const [isModalOpen, setIsModalOpen] = useState<boolean>(true)

  return (
    <FilePreview title="PDF Title" url="/dummy.pdf"  isOpen={isModalOpen} onClose={() => setIsModalOpen(false)} />
  )
}
