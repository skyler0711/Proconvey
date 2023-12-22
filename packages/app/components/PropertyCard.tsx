import Button from '@proconvey/ui/src/components/Button'
import { H3 } from '@proconvey/ui/src/components/Headers'
import PropertyTag from '@proconvey/ui/src/components/PropertyTag'
import { LocationIcon, ChevronDownIcon } from '@proconvey/ui/src/icons'
import { Property } from 'gql/graphql'
import Link from 'next/link'

type PropTypes = {
  property: {
    id: Property['id']
    address: Property['address']
    type: Property['type']
    conveyancer: {
      name: Property['conveyancer']['name']
    }
  }
}

const PropertyCard = ({ property }: PropTypes) => {
  return (
    <div className="p-5 w-full rounded-[0.625rem] bg-white border border-primary border-opacity-20">

      <div className="flex items-center gap-3 mt-[0.75rem] mb-[0.625rem]">
        <LocationIcon className="text-primary max-w-[18px] w-full max-h-[23px] h-full" />
        <H3 className="text-base">
          {property?.address?.line_1}
          {property?.address?.line_2 && `, ${property?.address?.line_2}`}
          {property?.address?.city && `, ${property?.address?.city}`}
          {property?.address?.postcode && `, ${property?.address?.postcode}`}
        </H3>
        {
          property.type &&
          <PropertyTag type={property.type}>{property.type}</PropertyTag>
        }
      </div>

      <p className="text-base font-medium text-body text-opacity-60">{property?.conveyancer?.name}</p>

      {/* <a href={`/properties/${property.id}/notifications`}>
        <div className="mt-[1.25rem]">
          <Tag variant="warning" className="flex justify-between w-full gap-5 border border-peach">
            <div className="flex items-center">
              <WarningIcon className="w-5 h-5 mr-2 text-peach" />
              <span className="text-mull/10">
                Notification for seller about the property
              </span>

            </div>
            <div className="flex items-center gap-[0.5rem]">View <ChevronDownIcon className="h-3 -rotate-90 text-peach" /></div>
          </Tag>
        </div>
      </a> */}

      <Link href="/notifications">
        <div className="flex items-center gap-1 mt-[1.25rem]">
          <button className="text-base text-primary">View all notifications</button>
          <ChevronDownIcon className="h-3 -rotate-90 text-primary" />
        </div>
      </Link>

      <Link
        href={`/properties/${property.id}`}
      >
        <Button variant="primary" className="mt-[1.25rem]">View Property</Button>
      </Link>
    </div>
  )
}

export default PropertyCard
