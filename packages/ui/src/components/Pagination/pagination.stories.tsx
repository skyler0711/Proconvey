import { ComponentStory, ComponentMeta } from '@storybook/react'
import Pagination from './index'

export default {
  title: 'Components/Pagination',
  component: Pagination,
} as ComponentMeta<typeof Pagination>

const Template: ComponentStory<typeof Pagination> = (args) => <Pagination {...args} />

export const PaginationExample: ComponentStory<typeof Pagination> = Template.bind({})

Pagination.args = {
  total: 9,
  currentPage: 1,
}
