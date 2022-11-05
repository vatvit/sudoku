provider "aws" {
  region  = var.aws_region
  shared_config_files = ["/Users/vatvit/.aws/config"]
  shared_credentials_files = ["/Users/vatvit/.aws/credentials"]
}
